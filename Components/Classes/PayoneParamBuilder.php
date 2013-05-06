<?php

/**
 * $Id: $
 */
class Mopt_PayoneParamBuilder
{

  const SEQUENCENUMBER_AUTH    = -1;
  const SEQUENCENUMBER_PREAUTH = 0;
  const SEQUENCENUMBER_CAPTURE = 1;

  /**
   * construct
   * @param type $payoneConfig
   */
  public function __construct($payoneConfig, $payoneHelper)
  {
    $this->payoneConfig = $payoneConfig;
    $this->payoneHelper = $payoneHelper;
  }

  /**
   * @TODO check encoding param
   * @TODO integrate possibility to use alternate configuration
   * 
   * returns auth-parameters for API-calls
   * @return string 
   */
  protected function getAuthParameters($paymentId = 0)
  {
    $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);

    $authParameters = array();

    $authParameters['mid']      = $this->payoneConfig['merchantId'];
    $authParameters['portalid'] = $this->payoneConfig['portalId'];
    $authParameters['key']      = $this->payoneConfig['apiKey'];
    $authParameters['aid']      = $this->payoneConfig['subaccountId'];

    if ($this->payoneConfig['liveMode'] == 1)
    {
      $authParameters['mode'] = 'live';
    }
    else
    {
      $authParameters['mode']     = 'test';
    }
    $authParameters['encoding'] = 'UTF-8'; // optional param default is: ISO-8859-1

    return $authParameters;
  }

  /**
   * build parameters for payment
   * @return array 
   */
  public function buildAuthorize($paymentId = 0)
  {
    $params = array();
    $params = $this->getAuthParameters($paymentId);

    return $params;
  }

  /**
   * build parameters for payment
   * @return array 
   */
  public function buildOrderCapture($order, $postionIds, $finalize)
  {
    $params = array();
    $payment     = $order->getPayment();
    $paymentName = $payment->getName();

    $params                   = array_merge($params, $this->getAuthParameters($payment->getId()));
    $params['txid']           = $order->getTransactionId();
    $params['sequencenumber'] = $this->getParamSequencenumber($order);
    $params['amount']         = $this->getParamCaptureAmount($order, $postionIds);
    $params['currency']       = $order->getCurrency();

    //create business object (used for settleaccount param)
    $business = new Payone_Api_Request_Parameter_Capture_Business();

    if ($paymentName === 'mopt_payone__acc_payinadvance' || preg_match('#mopt_payone__ibt#', $paymentName))
    {
      $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::YES : Payone_Api_Enum_Settleaccount::NO);
    }
    else
    {
      $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::YES : Payone_Api_Enum_Settleaccount::AUTO);
    }

    $params['business'] = $business;

    return $params;
  }

  /**
   * build parameters for debit
   * @return array 
   */
  public function buildOrderDebit($order, $postionIds)
  {
    $params = array();

    $params                   = array_merge($params, $this->getAuthParameters($order->getPayment()->getId()));
    $params['txid']           = $order->getTransactionId();
    $params['sequencenumber'] = $this->getParamSequencenumber($order);
    $params['amount']         = $this->getParamDebitAmount($order, $postionIds);
    $params['currency']       = $order->getCurrency();

    return $params;
  }

  /**
   * increase last seq-number for non-auth'ed orders
   * @param type $order
   * @return type
   * @throws Exception
   */
  protected function getParamSequencenumber($order)
  {
    $attribute = $this->payoneHelper->getOrCreateAttribute($order);
    $seqNo     = $attribute->getMoptPayoneSequencenumber();
    return $seqNo + 1;
  }

  protected function getParamDebitAmount($order, $positionIds)
  {
    return $this->getAmountFromPositions($order, $positionIds) * -1;
  }

  protected function getParamCaptureAmount($order, $positionIds)
  {
    return $this->getAmountFromPositions($order, $positionIds);
  }

  protected function getAmountFromPositions($order, $positionIds)
  {
    $amount = 0;
    if (empty($positionIds))
    {
      //return $order->getInvoiceAmount();
    }

    foreach ($order->getDetails() as $position)
    {
      if (!in_array($position->getId(), $positionIds))
      {
        continue;
      }

      $positionAttribute = $this->payoneHelper->getOrCreateAttribute($position);

      $alreadyCapturedAmount = $positionAttribute ? $positionAttribute->getMoptPayoneCaptured() : 0;

      //add difference between total price and already captured amount
      $amount += ($position->getPrice() * $position->getQuantity()) - $alreadyCapturedAmount;
    }

    return $amount;
  }

  public function buildBankaccountcheck($paymentId, $checkType, $languageId, $bankData)
  {
    $params = array();

    $params                = array_merge($params, $this->getAuthParameters($paymentId));
    $params['checktype']   = $checkType;
    $params['bankaccount'] = $bankData['mopt_payone__debit_bankaccount'];
    $params['bankcode']    = $bankData['mopt_payone__debit_bankcode'];
    $params['bankcountry'] = $bankData['mopt_payone__debit_bankcountry'];
    $params['language']    = $this->getLanguageFromCountryId($languageId);
    ;

    return $params;
  }

  /**
   * build parameters for payment
   * @return array 
   */
  public function getPersonalData($userData)
  {
    $params = array();

    $billingAddress = $userData['billingaddress'];

    $params['customerid'] = $userData['user']['customerId']; //@TODO check if it's better to use customernumber
//    $params['userid'] = ''; // payone userid
//    $params['salutation'] = $billingAddress['salutation'];
//    $params['title'] = ''; // optional and not available in shopware
    $params['firstname']  = $billingAddress['firstname'];
    $params['lastname']   = $billingAddress['lastname'];
    $params['company']    = $billingAddress['company'];
    $params['street']     = $billingAddress['street'] . ' ' . $billingAddress['streetnumber'];
//    $params['addressaddition'] = ''; // optional and not available in shopware
    $params['zip']        = $billingAddress['zipcode'];
    $params['city']       = $billingAddress['city'];
    $params['country']    = $userData['additional']['country']['countryiso'];

//    if (!empty($billingAddress['stateID'])) //@TODO check if correct key
//    {
//      $params['state']           = $this->getStateFromId($billingAddress['stateID']);
//    }
    $params['email']           = $userData['additional']['user']['email'];
    $params['telephonenumber'] = $billingAddress['phone'];
//    $params['birthday']        = $billingAddress['birthday']; //@TODO check if date needs to be converted
    $params['language']        = strtolower($userData['additional']['country']['countryiso']);
    $params['ustid']           = $billingAddress['firstname'];

    if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $clientIP = $_SERVER['REMOTE_ADDR'];
    }
    else
    {
      $clientIP     = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    $params['ip'] = $clientIP;


    $personalData = new Payone_Api_Request_Parameter_Authorization_PersonalData($params);

    return $personalData;
  }

  /**
   * build parameters for payment
   * @return array 
   */
  public function getDeliveryData($userData)
  {
    $params = array();
    $shippingAddress = $userData['shippingaddress'];

    $params['shipping_firstname'] = $shippingAddress['firstname'];
    $params['shipping_lastname']  = $shippingAddress['lastname'];
    $params['shipping_company']   = $shippingAddress['company'];
    $params['shipping_street']    = $shippingAddress['street'] . ' ' . $shippingAddress['streetnumber'];
    $params['shipping_zip']       = $shippingAddress['zipcode'];
    $params['shipping_city']      = $shippingAddress['city'];

//    if (!empty($shippingAddress['StateID'])) //@TODO check if correct key
//    {
//      $params['shipping_state']   = $this->getStateFromId($shippingAddress['StateID']);
//    }
    $params['shipping_country'] = $this->getCountryFromId($shippingAddress['countryID']);

    $personalData = new Payone_Api_Request_Parameter_Authorization_DeliveryData($params);

    return $personalData;
  }

  //@TODO check return urls
  public function getPaymentPaypal($router)
  {
    $params = array();

    $params['wallettype'] = 'PPE';
    $params['successurl'] = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
    $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
    $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));

    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
    return $payment;
  }

  /**
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment 
   */
  public function getPaymentDebitNote($paymentData)
  {
    $params = array();

    $params['bankcountry']       = $paymentData['mopt_payone__debit_bankcountry']; //DE, AT, NL
    $params['bankaccount']       = $paymentData['mopt_payone__debit_bankaccount'];
    $params['bankcode']          = $paymentData['mopt_payone__debit_bankcode'];
    $params['bankaccountholder'] = $paymentData['mopt_payone__debit_bankaccountholder']; //optional

    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment($params);
    return $payment;
  }

  /**
   * @TODO get bankdetails
   */
  public function getPaymentInstantBankTransfer($router, $paymentData)
  {
    $params = array();

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PNT')
    {
      $params['onlinebanktransfertype'] = 'PNT';
      $params['bankcountry']            = $paymentData['mopt_payone__sofort_bankcountry']; //DE, AT, NL
      $params['bankaccount']            = $paymentData['mopt_payone__sofort_bankaccount'];
      $params['bankcode']               = $paymentData['mopt_payone__sofort_bankcode'];
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => true));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => true));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => true));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'GPY')
    {
      $params['onlinebanktransfertype'] = 'GPY';
      $params['bankcountry']            = $paymentData['mopt_payone__giropay_bankcountry']; //DE, AT, NL
      $params['bankaccount']            = $paymentData['mopt_payone__giropay_bankaccount'];
      $params['bankcode']               = $paymentData['mopt_payone__giropay_bankcode'];
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'EPS')
    {
      $params['onlinebanktransfertype'] = 'EPS';
      $params['bankcountry']            = $paymentData['mopt_payone__eps_bankcountry'];
      $params['bankgrouptype']          = $paymentData['mopt_payone__eps_bankgrouptype'];
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'IDL')
    {
      $params['onlinebanktransfertype'] = 'IDL';
      $params['bankcountry']            = $paymentData['mopt_payone__ideal_bankcountry'];
      $params['bankgrouptype']          = $paymentData['mopt_payone__ideal_bankgrouptype'];
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PFF')
    {
      $params['onlinebanktransfertype'] = 'PFF';
      $params['bankcountry']            = 'CH';
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PFC')
    {
      $params['onlinebanktransfertype'] = 'PFC';
      $params['bankcountry']            = 'CH';
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));
    }

    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_OnlineBankTransfer($params);
    return $payment;
  }

  /**
   * create finance payment object
   */
  public function getPaymentFinance($financeType, $router)
  {
    $params = array();

    $params['financingtype'] = $financeType;
    $params['successurl']    = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => true));
    $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => true));
    $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => true));

    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing($params);
    return $payment;
  }

  /**
   * @TODO check for country -> should be done by config and risk management
   * @TODO get shipping provider
   *
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_CashOnDelivery 
   */
  public function getPaymentCashOnDelivery()
  {
    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CashOnDelivery();
    $payment->setShippingprovider('DHL'); // DE:DHL / IT:BRT

    return $payment;
  }

  /**
   * @TODO check if we use just the pseudocardpan
   */
  public function getPaymentCreditCard($router, $paymentData)
  {
    $params = array();

    $params['pseudocardpan'] = $paymentData['mopt_payone__cc_pseudocardpan'];
    $params['successurl']    = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
    $params['errorurl'] = $router->assemble(array('action'        => 'failure', 'forceSecure'   => true, 'appendSession' => false));

    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard($params);
    return $payment;
  }

  /**
   * @TODO fill all params
   * @TODO check really needed, complete params are not mandatory
   */
  public function getBusiness()
  {
    $params = array();

    $params['document_date'] = '';
    $params['booking_date']  = '';
    $params['due_time']      = '';

    $payment = new Payone_Api_Request_Parameter_Authorization_Business($params);
    return $payment;
  }

  /**
   * collect all items
   */
  public function getInvoicing($basket)
  {
    $params = array();
    $transaction = new Payone_Api_Request_Parameter_Invoicing_Transaction($params);

    foreach ($basket['content'] as $article)
    {
      $params = array();

      $params['id'] = $article['articleID']; //article number
      $params['pr'] = $article['priceNumeric']; //price
      $params['no'] = $article['quantity']; // ordered quantity
      $params['de'] = substr($article['articlename'], 0, 100); // description
      $params['va'] = $article['tax_rate']; // vat
      $params['it'] = Payone_Api_Enum_InvoicingItemType::GOODS; //item type
      if ($article['modus'] == 2)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
      }
      if ($article['modus'] == 4)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
      }
      $item         = new Payone_Api_Request_Parameter_Invoicing_Item($params);
      $transaction->addItem($item);
    }

    //add shipment as position
    $params = array();

    $params['id'] = 'Shipment'; //article number
    $params['pr'] = $basket['sShippingcostsWithTax']; //price
    $params['no'] = 1; // ordered quantity
    $params['de'] = 'Versandkosten'; // description check length
    $params['va'] = number_format($basket['sShippingcostsTax'], 0, '.', ''); // vat
    $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;

    $item = new Payone_Api_Request_Parameter_Invoicing_Item($params);
    $transaction->addItem($item);

    return $transaction;
  }

  /**
   * collect items from order
   */
  public function getInvoicingFromOrder($order, $positionIds, $finalize = 'skipCaptureMode')
  {
    $params = array();
    $transaction = new Payone_Api_Request_Parameter_Capture_Invoicing_Transaction($params);


    foreach ($order->getDetails() as $position)
    {
      if (!in_array($position->getId(), $positionIds))
      {
        continue;
      }

      $params = array();

      $params['id'] = $position->getArticleId(); //article number
      $params['pr'] = $position->getPrice() * $position->getQuantity(); //price
      $params['no'] = $position->getQuantity(); // ordered quantity
      $params['de'] = substr($position->getArticleName(), 0, 100); // description
      $params['va'] = $position->getTaxRate(); // vat
      $params['it'] = Payone_Api_Enum_InvoicingItemType::GOODS; //item type
      $mode         = $position->getMode();
      if ($mode == 2)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
      }
      if ($mode == 4)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
      }

      if ($position->getArticleNumber() == 'SHIPPING')
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;
      }
      $item         = new Payone_Api_Request_Parameter_Invoicing_Item($params);
      $transaction->addItem($item);
    }

    if ($finalize !== 'skipCaptureMode')
    {
      $transaction->setCapturemode($finalize ? Payone_Api_Enum_CaptureMode::COMPLETED : Payone_Api_Enum_CaptureMode::NOTCOMPLETED);
    }

    return $transaction;
  }

  /**
   * @param type $billingFormData
   * @param type $personalFormData
   * @return type 
   */
  public function getAddressCheckParams($billingFormData, $personalFormData, $paymentId = 0)
  {
    $params = array();
    $params = $this->getAuthParameters($paymentId);

    $params['firstname']       = $personalFormData['firstname'];
    $params['lastname']        = $personalFormData['lastname'];
    $params['company']         = $billingFormData['company'];
    $params['street']          = $billingFormData['street'] . ' ' . $billingFormData['streetnumber'];
    $params['streetname']      = $billingFormData['street'];
    $params['streetnumber']    = $billingFormData['streetnumber'];
    $params['zip']             = $billingFormData['zipcode'];
    $params['city']            = $billingFormData['city'];
//    if (!empty($billingFormData['state'])) //@TODO check if correct key, only for US and Canada
//    {
//      $params['state']           = $this->getStateFromId($billingFormData['state']);
//    }
    $params['country']         = $this->getCountryFromId($billingFormData['country']);
    $params['telephonenumber'] = $personalFormData['phone'];
    $params['language']        = $this->getLanguageFromCountryId($billingFormData['country']);

    return $params;
  }

  /**
   * @param type $billingFormData
   * @return type 
   */
  public function getConsumerscoreCheckParams($billingFormData, $paymentId = 0)
  {
    $params = $this->getAuthParameters($paymentId);

    $params['firstname']    = $billingFormData['firstname'];
    $params['lastname']     = $billingFormData['lastname'];
    $params['company']      = $billingFormData['company'];
    $params['street']       = $billingFormData['street'] . ' ' . $billingFormData['streetnumber'];
    $params['streetname']   = $billingFormData['street'];
    $params['streetnumber'] = $billingFormData['streetnumber'];
    $params['zip']          = $billingFormData['zipcode'];
    $params['city']         = $billingFormData['city'];
//    if (!empty($billingFormData['state'])) //@TODO check if correct key
//    {
//      $params['state']           = $this->getStateFromId($billingFormData['state']);
//    }
    $params['country']      = $this->getCountryFromId($billingFormData['countryID']);
//    $params['telephonenumber'] = $personalFormData['phone'];
    $params['language']     = $this->getLanguageFromCountryId($billingFormData['countryID']);

    return $params;
  }

  protected function getCountryFromId($id)
  {
    $sql     = 'SELECT `countryiso` FROM s_core_countries WHERE id = ' . $id;
    $country = Shopware()->Db()->fetchOne($sql);
    return $country;
  }

  protected function getLanguageFromCountryId($id)
  {
    $sql      = 'SELECT `countryiso` FROM s_core_countries WHERE id = ' . $id;
    $language = Shopware()->Db()->fetchOne($sql);

    return strtolower($language);
  }

  protected function getStateFromId($id)
  {
    $sql   = 'SELECT `shortcode` FROM s_core_countries_states WHERE id = ' . $id;
    $state = Shopware()->Db()->fetchOne($sql);

    return $state;
  }

  public function getParamPaymentReference()
  {
    return 'mopt_' . uniqid() . rand(10, 99);
  }

}