<?php

/**
 * updated and finish transactions
 */
class Shopware_Controllers_Frontend_MoptShopNotification extends Shopware_Controllers_Frontend_Payment
{

    protected $moptPayone__serviceBuilder = null;
    protected $moptPayone__main = null;
    protected $moptPayone__helper = null;
    protected $moptPayone__paymentHelper = null;
    protected $logger = null;

    /**
     * init notification controller for processing status updates
     */
    public function init()
    {
        $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayone__main = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayone__helper = $this->moptPayone__main->getHelper();
        $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();

        $this->logger = new Monolog\Logger('moptPayone');
        $streamHandler = new Monolog\Handler\StreamHandler(Shopware()->Application()->Kernel()->getLogDir()
                . '/moptPayoneTransactionStatus.log', Monolog\Logger::ERROR);
        $this->logger->pushHandler($streamHandler);

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }

    /**
     * index action called by Payone platform to transmit transaction status updates
     * 
     * @return mixed
     */
    public function indexAction()
    {
        $request = $this->Request();

        if (!$request->isPost()) {
            $this->redirect(array('controller' => 'index', 'action' => 'error'));
            return;
        }

        $rawPost = $_POST;
        $_POST = array_map('utf8_encode', $_POST); // utf8 encode all post params to avoid encoding issues
        $request->setParamSources(array('_POST')); // only retrieve data from POST

        $transactionId = $request->getParam('txid');
        $isOrderFinished = $this->isOrderFinished($transactionId,  $request->getParam('reference'));

        if ($isOrderFinished) {
            $order = $this->loadOrderByTransactionId($transactionId);
            $paymentId = $order->getPayment()->getId();
        } else {
            $this->restoreSession($request->getParam('param'));
            $session = Shopware()->Session();
            $orderVariables = $session['sOrderVariables']->getArrayCopy();
            $paymentId = $orderVariables['sUserData']['additional']['user']['paymentID'];
        }

        $config = $this->moptPayone__main->getPayoneConfig($paymentId, true);
        Shopware()->Config()->mopt_payone__paymentId = $paymentId; //store in config for log
        $key = $config['apiKey'];

        $moptConfig = new Mopt_PayoneConfig();
        $validIps = $moptConfig->getValidIPs();

        $service = $this->moptPayoneInitTransactionService($key, $validIps);

        try {
            $response = $service->handleByPost();
        } catch (Exception $exc) {
                $this->logger->error('error processing request', array($exc->getTraceAsString()));
            echo 'error processing request';
            exit;
        }
        
        $orderIsCorrupted = false;

        if (!$isOrderFinished) {
            $orderHash = md5(serialize($session['sOrderVariables']));
            $customParam = explode('|', $request->getParam('param'));
            
            if ($orderHash !== $customParam[2]) {
                $this->logger->error('order corrupted - order hash mismatch');
                $orderIsCorrupted = true;
                $paymentStatus = 21;
                $orderNumber = $this->saveOrder($transactionId, $request->getParam('reference'), $paymentStatus);
            } else {
                $orderNumber = $this->saveOrder($transactionId, $request->getParam('reference'));
            }
            
            $order = $this->loadOrderByOrderNumber($orderNumber);
        } 

        $payoneRequest = $service->getMapper()->mapByArray($request->getPost());
        $attributeData = $this->moptPayone__helper->getOrCreateAttribute($order);
        $attributeData->setMoptPayoneStatus($request->getParam('txaction'));
        $attributeData->setMoptPayoneSequencenumber($payoneRequest->getSequencenumber());
        $attributeData->setMoptPayonePaymentReference($request->getParam('reference'));
        if(isset($customParam[2])) {
            $attributeData->setMoptPayoneOrderHash($customParam[2]);
        }

        $clearingData = $this->moptPayone__paymentHelper->extractClearingDataFromResponse($payoneRequest);
        if ($clearingData) {
            $clearingData = json_encode($clearingData);
            $attributeData->setMoptPayoneClearingData($clearingData);
        }

        Shopware()->Models()->persist($attributeData);
        Shopware()->Models()->flush();

        if (!$orderIsCorrupted) {
            $mappedShopwareState = $this->moptPayone__helper->getMappedShopwarePaymentStatusId(
                    $config, $request->getParam('txaction'));

            $this->savePaymentStatus($transactionId, $order->getTemporaryId(), $mappedShopwareState);
        }

        echo $response->getStatus();
        $this->moptPayoneForwardTransactionStatus($config, $rawPost, $request->getParam('txaction'));

        exit;
    }

    /**
     * get transaction service, validate key and ip addresses
     * 
     * @param string $key
     * @param array $validIps
     * @return service
     */
    protected function moptPayoneInitTransactionService($key, $validIps)
    {
        $hashedKey = md5($key);
        $service = $this->moptPayone__serviceBuilder->buildServiceTransactionStatusHandleRequest($hashedKey, $validIps);

        $repository = Shopware()->Models()
                ->getRepository('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog');
        $service->getServiceProtocol()->addRepository($repository);

        return $service;
    }

    /**
     * forward request to configured urls
     * 
     * @param array $payoneConfig
     * @param array $rawPost
     * @param string $payoneStatus
     */
    protected function moptPayoneForwardTransactionStatus($payoneConfig, $rawPost, $payoneStatus)
    {
        $configKey = 'trans' . ucfirst($payoneStatus);
        if (isset($payoneConfig[$configKey])) {
            $forwardingUrls = explode(';', $payoneConfig[$configKey]);

            $zendClientConfig = array(
                'adapter' => 'Zend_Http_Client_Adapter_Curl',
                'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
            );

            foreach ($forwardingUrls as $url) {
                if (empty($url)) {
                    continue;
                }

                $client = new Zend_Http_Client($url, $zendClientConfig);
                $client->setConfig(array('timeout' => 60));
                $client->setParameterPost($rawPost);
                $client->request(Zend_Http_Client::POST);
            }
        }
    }

    /**
     * get plugin bootstrap
     * 
     * @return plugin
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * try to load order via transaction id
     * 
     * @param string $transactionId
     * @return order
     */
    protected function loadOrderByTransactionId($transactionId)
    {
        return Shopware()->Models()->getRepository('Shopware\Models\Order\Order')
                        ->findOneBy(array('transactionId' => $transactionId));
    }

    /**
     * try to load order via transaction id
     * 
     * @param string $orderNumber
     * @return order
     */
    protected function loadOrderByOrderNumber($orderNumber)
    {
        return Shopware()->Models()->getRepository('Shopware\Models\Order\Order')
                        ->findOneBy(array('number' => $orderNumber));
    }

    /**
     * restore session from Id
     * 
     * @param string $customParam
     */
    protected function restoreSession($customParam)
    {
        $sessionParam = explode('|', $customParam);
        
        \Enlight_Components_Session::writeClose();
        \Enlight_Components_Session::setId($sessionParam[1]);
        \Enlight_Components_Session::start();
    }

    /**
     * determine wether order is already finished
     * 
     * @param string $transactionId
     * @param string $paymentReference
     * @return boolean
     */
    protected function isOrderFinished($transactionId, $paymentReference)
    {
        $sql = '
            SELECT ordernumber FROM s_order
            WHERE transactionID=? AND status!=-1';

        $orderNumber = Shopware()->Db()->fetchOne($sql, array($transactionId));

        if (empty($orderNumber) && !$this->isFinishedWithReference($paymentReference, $transactionId)) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * determine wether order is already finished
     * additional check for frontend api creditcard payments
     * 
     * @param string $paymentReference
     * @param string $transactionId
     * @return boolean
     */
    protected function isFinishedWithReference($paymentReference, $transactionId)
    {
        $sql = '
            SELECT ordernumber FROM s_order
            WHERE transactionID=? AND status!=-1';

        $orderNumber = Shopware()->Db()->fetchOne($sql, array($paymentReference));

        if (empty($orderNumber)) {
            return false;
        } else {
            $this->setTransactionId($orderNumber, $transactionId);
            return true;
        }
    }

    /**
     * update transaction id, needed for frontend api creditcard payments
     * 
     * @param string $orderNumber
     * @param string $transactionId
     * @return boolean
     */
    protected function setTransactionId($orderNumber, $transactionId)
    {
        $sql = '
            UPDATE s_order SET transactionID=?
            WHERE ordernumber=?';

        Shopware()->Db()->query($sql, array($transactionId, $orderNumber));
    }

}
