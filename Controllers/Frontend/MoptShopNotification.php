<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Frontend_MoptShopNotification extends Shopware_Controllers_Frontend_Payment
{

  protected $moptPayone__serviceBuilder = null;
  protected $moptPayone__main           = null;
  protected $moptPayone__helper         = null;
  protected $moptPayone__paymentHelper  = null;

  /**
   *  Quote: "Der SessionStatus wird von folgenden IP-Adressen 
   * aus verschickt: 213.178.72.196 bzw. 213.178.72.197 sowie 217.70.200.0/24."
   */
  public function init()
  {
    $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->PayoneBuilder();
    $this->moptPayone__main           = $this->Plugin()->Application()->PayoneMain();
    $this->moptPayone__helper         = $this->moptPayone__main->getHelper();
    $this->moptPayone__paymentHelper  = $this->moptPayone__main->getPaymentHelper();

    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
  }

  public function indexAction()
  {
    $request = $this->Request();

    // 404 if no POST request
    if (!$request->isPost())
    {
      $this->redirect(array('controller' => 'index', 'action' => 'error'));
      return;
    }

    //utf8 encode all post params to avoid encoding issues
    $_POST = array_map('utf8_encode', $_POST);
    
    // only retrieve data from POST
    $request->setParamSources(array('_POST'));

    // get reference (order)
    $transactionId = $request->getParam('txid');

    //load order by reference
    $order = $this->moptLoadOrderByTransactionId($transactionId);

    if (!$order)
    {
      echo 'Order not found. ' . $transactionId;
      exit;
    }

    //get paymentid from referenced order
    $paymentId = $order->getPayment()->getId();
    Shopware()->Config()->mopt_payone__paymentId = $paymentId; //store in config for log
    //get key from config
    $config    = $this->moptPayone__main->getPayoneConfig($paymentId, true);
    $key       = $config['apiKey'];

    $moptConfig = new Mopt_PayoneConfig();
    $validIps = $moptConfig->getValidIPs();
    
    $service  = $this->moptPayoneInitTransactionService($key, $validIps);
    $response = $service->handleByPost();

    $payoneRequest = $service->getMapper()->mapByArray($request->getPost());
    
    if ($response->getStatus() == $response::STATUS_OK)
    {
      //check if order is already finished
      $orderNumber = $order->getNumber();
      if(empty($orderNumber))
      {
        $this->moptFinishOrder($payoneRequest->getParam());
        $order = $this->moptLoadOrderByTransactionId($transactionId);
        $orderNumber = $order->getNumber();
        if(empty($orderNumber))
        {
          echo 'failure - no order nr found.';
          exit;
        }
      }
        
      $attributeData = $this->moptPayone__helper->getOrCreateAttribute($order);
      $attributeData->setMoptPayoneStatus($request->getParam('txaction'));
      $attributeData->setMoptPayoneSequencenumber($payoneRequest->getSequencenumber());

      $clearingData = $this->moptPayone__paymentHelper->extractClearingDataFromResponse($payoneRequest);
      if ($clearingData)
      {
        $clearingData = json_encode($clearingData);
        $attributeData->setMoptPayoneClearingData($clearingData);
      }

      Shopware()->Models()->persist($attributeData);
      Shopware()->Models()->flush();

      $mappedShopwareState = $this->moptPayone__helper->getMappedShopwarePaymentStatusId(
              $config, $request->getParam('txaction'));
      
      $this->savePaymentStatus($transactionId, $order->getTemporaryId(), $mappedShopwareState);
    }

    echo $response->getStatus();

    if ($response->getStatus() == $response::STATUS_OK)
    {
      // forward status to configured urls
      $this->moptPayoneForwardTransactionStatus($config, $payoneRequest, $request->getParam('txaction'));
    }

    exit;
  }

  protected function moptPayoneInitTransactionService($key, $validIps)
  {
    $key     = md5($key);
    $service = $this->moptPayone__serviceBuilder->buildServiceTransactionStatusHandleRequest($key, $validIps);

    $repository = Shopware()->Models()
            ->getRepository('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog');
    $service->getServiceProtocol()->addRepository($repository);

    return $service;
  }

  protected function moptPayoneForwardTransactionStatus($payoneConfig, $request, $payoneStatus)
  {
    //check if urls are configured for this status
    $configKey = 'trans' . ucfirst($payoneStatus);
    if (isset($payoneConfig[$configKey]))
    {
      $forwardingUrls = explode(';', $payoneConfig[$configKey]);

      $params = $request->toArray();

      //configure ZEND Http Client
      $zendClientConfig = array(
          'adapter'     => 'Zend_Http_Client_Adapter_Curl',
          'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
      );
      
      //send transaction to each url
      foreach ($forwardingUrls as $url)
      {
        if (empty($url))
        {
          continue;
        }

        // new HTTP request to some HTTP address
        $client = new Zend_Http_Client($url, $zendClientConfig);
        // set Timeout
        $client->setConfig(array('timeout' => 60));

        // set parameters
        $client->setParameterPost($params);

        // POST request
        $client->request(Zend_Http_Client::POST);
      }
    }
  }

  public function Plugin()
  {
    return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
  }

  /**
   * finalize order
   * 
   * @param string $sessionParam
   */
  protected function moptFinishOrder($sessionParam)
  {
    $session = explode('|', $sessionParam);
    $router = $this->Front()->Router();
      
    $url = $router->assemble(array('controller' => 'MoptPaymentPayone', 'action' => 'success', 
      'forceSecure' => true, 'appendSession' => false));
      
    //configure ZEND Http Client
    $zendClientConfig = array(
        'adapter'     => 'Zend_Http_Client_Adapter_Curl',
        'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
        'timeout' => 10,
    );

    // new HTTP request
    $client = new Zend_Http_Client($url, $zendClientConfig);
    // set parameter
    $client->setParameterGet($session[0], $session[1]);
    // GET request
    $client->request(Zend_Http_Client::GET);
  }
  
  protected function moptLoadOrderByTransactionId($transactionId)
  {
    return Shopware()->Models()->getRepository('Shopware\Models\Order\Order')
            ->findOneBy(array('transactionId' => $transactionId));
  }
}
