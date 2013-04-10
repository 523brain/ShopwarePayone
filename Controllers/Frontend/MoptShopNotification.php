<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Frontend_MoptShopNotification extends Enlight_Controller_Action
{

  protected $moptPayone__serviceBuilder = null;
  protected $moptPayone__main = null;
  protected $moptPayone__helper = null;

  /**
   *  Quote: "Der SessionStatus wird von folgenden IP-Adressen aus verschickt: 213.178.72.196 bzw. 213.178.72.197 sowie 217.70.200.0/24."
   * 
   *  We may have problems with advanced network-infrastructure (load-balancing etc) ?!
   */
  public function init()
  {
    $this->moptPayone__serviceBuilder = $this->Plugin()->Application()->PayoneBuilder();
    $this->moptPayone__main           = $this->Plugin()->Application()->PayoneMain();
    $this->moptPayone__helper         = $this->moptPayone__main->getHelper();
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
      echo 'Order not found. ' . $transactionId; exit;
    }

    //get paymentid from referenced order
    $paymentId = $order->getPayment()->getId();
    Shopware()->Config()->mopt_payone__paymentId = $paymentId; //store in config for log
    
    //get key from config
    $config = $this->moptPayone__main->getPayoneConfig($paymentId, true);
    $key = $config['apiKey'];
    
    //@todo: get valid ips from config ?!
    //IP-validator commented out in Components/Payone/Config.php
    $validIps = array();
    
    $service = $this->moptPayoneInitTransactionService($key, $validIps);

    $response = $service->handleByPost();

    if($response->getStatus() == $response::STATUS_OK)
    {
      $attributeData = $this->moptPayone__helper->getOrCreateAttribute($order);
      
      //do not insert new attribute row => weird session bug
      if($attributeData->getId())
      {
        $attributeData->setMoptPayoneStatus($request->getParam('txaction'));
        Shopware()->Models()->persist($attributeData);
        Shopware()->Models()->flush();
      }
      
      $this->moptPayone__helper->mapTransactionStatus($order, $config, $request->getParam('txaction'));
    }
    
    echo $response->getStatus();
    exit;
  }
  
  protected function moptPayoneInitTransactionService($key, $validIps)
  {
    $key     = md5($key);
    $service = $this->moptPayone__serviceBuilder->buildServiceTransactionStatusHandleRequest($key, $validIps);

    $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog');
    $service->getServiceProtocol()->addRepository($repository);

    return $service;
  }

  public function Plugin()
  {
    return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
  }
}