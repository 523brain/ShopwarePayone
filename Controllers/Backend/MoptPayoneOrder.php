<?php

class Shopware_Controllers_Backend_MoptPayoneOrder extends Shopware_Controllers_Backend_ExtJs
{

  protected $moptPayone__sdk__Builder = null;
  protected $moptPayone__main         = null;
  protected $moptPayone__helper       = null;

  public function init()
  {
    $this->moptPayone__sdk__Builder = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->PayoneBuilder();
    $this->moptPayone__main = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->Application()->PayoneMain();
    $this->moptPayone__helper = $this->moptPayone__main->getHelper();
  }

  public function moptPayoneDebitAction()
  {
    $request = $this->Request();

    try
    {
      //get id
      $orderId = $request->getParam('id');

      if (!$order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId))
      {
        throw new Exception("Order not found.");
      }

      if (!$this->moptPayone_isOrderDebitable($order))
      {
        throw new Exception("Gutschrift nicht möglich.");
      }
      $payment     = $order->getPayment();
      $paymentName = $payment->getName();

      //positions ?
      $positionIds = $request->get('positionIds') ? json_decode($request->get('positionIds')) : array();

      //fetch params
      $params = $this->moptPayone__main->getParamBuilder()->buildOrderDebit($order, $positionIds);

      if (preg_match('#mopt_payone__fin_billsafe#', $paymentName))
      {
        $invoicing = $this->moptPayone__main->getParamBuilder()->getInvoicingFromOrder($order, $positionIds, 'skipCaptureMode', true);
      }

      //call capture service
      $response = $this->moptPayone_callDebitService($params, $invoicing);

      if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED)
      {
        //increase sequence
        $this->moptPayoneUpdateSequenceNumber($order, true);

        //mark / fill positions as captured
        $this->moptPayoneMarkPositionsAsDebited($order, $positionIds);

        $response = array('success' => true);
      }
      else
      {
        //show error message, don't show PAYONE error message to shop owner
        $response = array('success'       => false, 'error_message' => 'Gutschrift (zur Zeit) nicht möglich.');
      }
    }
    catch (Exception $e)
    {
      $response = array('success'       => false, 'error_message' => $e->getMessage());
    }

    $this->View()->assign($response);
  }

  public function moptPayoneCaptureOrderAction()
  {
    $request = $this->Request();

    try
    {
      //get id
      $orderId = $request->getParam('id');

      if (!$order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId))
      {
        throw new Exception("Order not found.");
      }

      if (!$this->moptPayone_isOrderCapturable($order))
      {
        throw new Exception("Capture nicht möglich.");
      }

      $payment     = $order->getPayment();
      $paymentName = $payment->getName();

      //positions ?
      $positionIds = $request->get('positionIds') ? json_decode($request->get('positionIds')) : array();

      //covert finalize param
      $finalize = $request->get('finalize') == "true" ? true : false;

      //fetch params
      $params = $this->moptPayone__main->getParamBuilder()->buildOrderCapture($order, $positionIds, $finalize);

      if (preg_match('#mopt_payone__fin_billsafe#', $paymentName))
      {
        $invoicing = $this->moptPayone__main->getParamBuilder()->getInvoicingFromOrder($order, $positionIds, $finalize);
      }

      //call capture service
      $response = $this->moptPayone_callCaptureService($params, $invoicing);

      if ($response->getStatus() == Payone_Api_Enum_ResponseType::APPROVED)
      {
        //increase sequence
        $this->moptPayoneUpdateSequenceNumber($order, true);

        //mark / fill positions as captured
        $this->moptPayoneMarkPositionsAsCaptured($order, $positionIds);

        //extract and save clearing data
        $clearingData = $this->moptPayone__helper->extractClearingDataFromResponse($response);
        if ($clearingData)
        {
          $this->moptPayoneSaveClearingData($order, $clearingData);
        }

        $response = array('success' => true);
      }
      else
      {
        //show error message, don't show PAYONE error message to shop owner
        $response = array('success'       => false, 'error_message' => 'Capture (zur Zeit) nicht möglich.');
      }
    }
    catch (Exception $e)
    {
      $response = array('success'       => false, 'error_message' => $e->getMessage());
    }

    $this->View()->assign($response);
  }

  protected function moptPayoneUpdateSequenceNumber($order, $isAuth = false)
  {
    $attribute = $this->moptPayone__helper->getOrCreateAttribute($order);
    $newSeq    = $attribute->getMoptPayoneSequencenumber() + 1;
    $attribute->setMoptPayoneSequencenumber($newSeq);
    if ($isAuth)
    {
      $attribute->setMoptPayoneIsAuthorized(true);
    }

    Shopware()->Models()->persist($attribute);
    Shopware()->Models()->flush();
  }

  protected function moptPayone_isOrderCapturable($order)
  {
    if (!$this->moptPayone_hasOrderPayonePayment($order))
    {
      return false;
    }

    //according to PAYONE, perform less checks in shop, let the API validate
    $attribute = $this->moptPayone__helper->getOrCreateAttribute($order);
//    
//    //is already authorized ? 
//    if($attribute->getMoptPayoneIsAuthorized())
//    {
//      return false;
//    }

    return true;
  }

  protected function moptPayone_callCaptureService($params, $invoicing = null)
  {
    $service = $this->moptPayone__sdk__Builder->buildServicePaymentCapture();

    $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog');
    $service->getServiceProtocol()->addRepository($repository);

    $request = new Payone_Api_Request_Capture($params);

    if ($invoicing)
    {
      $request->setInvoicing($invoicing);
    }

    return $service->capture($request);
  }

  protected function moptPayoneMarkPositionsAsCaptured($order, $positionIds)
  {
    foreach ($order->getDetails() as $position)
    {
      if (!in_array($position->getId(), $positionIds))
      {
        continue;
      }

      $attribute = $this->moptPayone__helper->getOrCreateAttribute($position);
      $attribute->setMoptPayoneCaptured($position->getPrice() * $position->getQuantity());

      Shopware()->Models()->persist($attribute);
      Shopware()->Models()->flush();
    }
  }

  protected function moptPayone_isOrderDebitable($order)
  {
    if (!$this->moptPayone_hasOrderPayonePayment($order))
    {
      return false;
    }

    return true;
  }

  protected function moptPayone_hasOrderPayonePayment($order)
  {
    //order has Payone-Payment ?
    if (strpos($order->getPayment()->getName(), 'mopt_payone__') !== 0)
    {
      return false;
    }

    return true;
  }

  protected function moptPayone_callDebitService($params, $invoicing = null)
  {
    $service = $this->moptPayone__sdk__Builder->buildServicePaymentDebit();

    $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog');
    $service->getServiceProtocol()->addRepository($repository);

    $request = new Payone_Api_Request_Debit($params);

    if ($invoicing)
    {
      $request->setInvoicing($invoicing);
    }

    return $service->debit($request);
  }

  protected function moptPayoneMarkPositionsAsDebited($order, $positionIds)
  {
    foreach ($order->getDetails() as $position)
    {
      if (!in_array($position->getId(), $positionIds))
      {
        continue;
      }

      $attribute = $this->moptPayone__helper->getOrCreateAttribute($position);
      $attribute->setMoptPayoneDebit($position->getPrice() * $position->getQuantity());

      Shopware()->Models()->persist($attribute);
      Shopware()->Models()->flush();
    }
  }

  protected function moptPayoneSaveClearingData($order, $clearingData)
  {
    $attribute    = $this->moptPayone__helper->getOrCreateAttribute($order);
    $clearingData = json_encode($clearingData);
    $attribute->setMoptPayoneClearingData($clearingData);

    Shopware()->Models()->persist($attribute);
    Shopware()->Models()->flush();
  }

}