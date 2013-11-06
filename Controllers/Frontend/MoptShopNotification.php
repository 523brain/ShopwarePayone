<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Frontend_MoptShopNotification extends Enlight_Controller_Action
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
    $this->moptPayone__main = $this->Plugin()->Application()->PayoneMain();
    $this->moptPayone__helper = $this->moptPayone__main->getHelper();
    $this->moptPayone__paymentHelper = $this->moptPayone__main->getPaymentHelper();

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

    // only retrieve data from POST
    $request->setParamSources(array('_POST'));

    // get reference (order)
    $transactionId = $request->getParam('txid');

    //load order by reference
    $order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')
            ->findOneBy(array('transactionId' => $transactionId));

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
    
    $service = $this->moptPayoneInitTransactionService($key, $validIps);

    $response      = $service->handleByPost();
    $payoneRequest = $service->getMapper()->mapByArray($request->getPost());
    
    if ($response->getStatus() == $response::STATUS_OK)
    {
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

      $this->moptPayone__helper->mapTransactionStatus($order, $config, $request->getParam('txaction'));
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

      //send transaction to each url
      foreach ($forwardingUrls as $url)
      {
        if (empty($url))
        {
          continue;
        }

        //configure ZEND Http Client
        $zendClientConfig = array(
            'adapter'     => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(CURLOPT_FOLLOWLOCATION => true),
        );

        // new HTTP request to some HTTP address
        $client = new Zend_Http_Client($url, $zendClientConfig);
        // set Timeout
        $client->setConfig(array('timeout' => 60));

        // set parameters
        $client->setParameterPost($params);

        // POST request
        $response = $client->request(Zend_Http_Client::POST);

        if ($response->getBody() != 'TSOK')
        {
          //@TODO log errors
        }
      }
    }
  }

  public function Plugin()
  {
    return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
  }

}