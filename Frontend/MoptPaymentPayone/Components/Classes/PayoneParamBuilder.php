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
   * Payone Config
   * @var array
   */
  protected $payoneConfig = null;

  /**
   * Payone Helper
   * @var Mopt_PayoneHelper 
   */
  protected $payoneHelper = null;

  /**
   * Payone Payment Helper
   * @var Mopt_PayonePaymentHelper 
   */
  protected $payonePaymentHelper = null;

  /**
   * constructor, sets config and helper class
   *
   * @param array $payoneConfig
   * @param object $payoneHelper 
   */
  public function __construct($payoneConfig, $payoneHelper, $payonePaymentHelper)
  {
    $this->payoneConfig = $payoneConfig;
    $this->payoneHelper = $payoneHelper;
    $this->payonePaymentHelper = $payonePaymentHelper;
  }

  /**
   * returns auth-parameters for API-calls
   *
   * @param string $paymentId
   * @return array 
   */
  protected function getAuthParameters($paymentId = 0)
  {
    $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);

    $authParameters = array();

    $authParameters['mid']      = $this->payoneConfig['merchantId'];
    $authParameters['portalid'] = $this->payoneConfig['portalId'];
    $authParameters['key']      = $this->payoneConfig['apiKey'];
    $authParameters['aid']      = $this->payoneConfig['subaccountId'];

    $authParameters['solution_name']      = 'mediaopt';
    $authParameters['solution_version']   = Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap::getVersion();
    $authParameters['integrator_name']    = 'shopware';
    $authParameters['integrator_version'] = Shopware()->Config()->Version;

    if ($this->payoneConfig['liveMode'] == 1)
    {
      $authParameters['mode'] = Payone_Enum_Mode::LIVE;
    }
    else
    {
      $authParameters['mode'] = Payone_Enum_Mode::TEST;
    }
    $authParameters['encoding'] = 'UTF-8'; // optional param default is: ISO-8859-1

    return $authParameters;
  }

  /**
   * build parameters for payment
   * 
   * @return array 
   */
  public function buildAuthorize($paymentId = 0)
  {
    $params = array();
    $params = $this->getAuthParameters($paymentId);

    return $params;
  }

  /**
   * returns params to capture orders
   *
   * @param object $order
   * @param array $postionIds
   * @param bool $finalize
   * @return \Payone_Api_Request_Parameter_Capture_Business 
   */
  public function buildOrderCapture($order, $postionIds, $finalize)
  {
    $params = array();
    $paymentName = $order->getPayment()->getName();

    $params                   = array_merge($params, $this->getAuthParameters($order->getPayment()->getId()));
    $params['txid']           = $order->getTransactionId();
    $params['sequencenumber'] = $this->getParamSequencenumber($order);
    $params['amount']         = $this->getParamCaptureAmount($order, $postionIds);
    $params['currency']       = $order->getCurrency();

    //create business object (used for settleaccount param)
    $business = new Payone_Api_Request_Parameter_Capture_Business();

    if ($this->payonePaymentHelper->isPayonePayInAdvance($paymentName) || $this->payonePaymentHelper->isPayoneInstantBankTransfer($paymentName))
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
   *
   * @param object $order
   * @param array $postionIds
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
   * 
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

      //add difference between total price and already captured amount
      $amount += ($position->getPrice() * $position->getQuantity());
    }

    return $amount * -1;
  }

  /**
   * return amount to capture
   *
   * @param object $order
   * @param array $positionIds
   * @return string 
   */
  protected function getParamCaptureAmount($order, $positionIds)
  {
    return $this->getAmountFromPositions($order, $positionIds);
  }

  /**
   * get amount from positions
   *
   * @param object $order
   * @param array $positionIds
   * @return string 
   */
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

  /**
   * build params for bankaccount check
   *
   * @param string $paymentId
   * @param string $checkType
   * @param string $languageId
   * @param array $bankData
   * @return array 
   */
  public function buildBankaccountcheck($paymentId, $checkType, $languageId, $bankData)
  {
    $params = array();

    $params                = array_merge($params, $this->getAuthParameters($paymentId));
    $params['checktype']   = $checkType;
    $params['bankaccount'] = $this->removeWhitespaces($bankData['mopt_payone__debit_bankaccount']);
    $params['bankcode']    = $this->removeWhitespaces($bankData['mopt_payone__debit_bankcode']);
    $params['bankcountry'] = $bankData['mopt_payone__debit_bankcountry'];
    $params['language']    = $this->getLanguageFromCountryId($languageId);
    ;

    return $params;
  }

  /**
   * build personal data parameters 
   *
   * @param array $userData
   * @return \Payone_Api_Request_Parameter_Authorization_PersonalData 
   */
  public function getPersonalData($userData)
  {
    $params = array();

    $billingAddress = $userData['billingaddress'];

    $params['customerid'] = $billingAddress['customernumber'];
    $params['firstname']  = $billingAddress['firstname'];
    $params['lastname']   = $billingAddress['lastname'];
    $params['company']    = $billingAddress['company'];
    $params['street']     = $billingAddress['street'] . ' ' . $billingAddress['streetnumber'];
    $params['zip']        = $billingAddress['zipcode'];
    $params['city']       = $billingAddress['city'];
    $params['country']    = $userData['additional']['country']['countryiso'];
    if (!empty($billingAddress['stateID']))
    {
      $params['state']           = $this->getStateFromId($billingAddress['stateID'], $params['country']);
    }
    $params['email']           = $userData['additional']['user']['email'];
    $params['telephonenumber'] = $billingAddress['phone'];
    $params['language']        = $this->getLanguageFromCountryId($userData['additional']['country']['id']);
    $params['vatid']           = $billingAddress['ustid'];
    $params['ip']              = $_SERVER['REMOTE_ADDR'];

    $personalData = new Payone_Api_Request_Parameter_Authorization_PersonalData($params);

    return $personalData;
  }

  /**
   * build parameters for payment
   *
   * @param array $userData
   * @return \Payone_Api_Request_Parameter_Authorization_DeliveryData 
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
    $params['shipping_country']   = $this->getCountryFromId($shippingAddress['countryID']);
    if (!empty($shippingAddress['stateID']))
    {
      $params['shipping_state'] = $this->getStateFromId($shippingAddress['stateID'], $params['shipping_country']);
    }

    $personalData = new Payone_Api_Request_Parameter_Authorization_DeliveryData($params);

    return $personalData;
  }

  /**
   * returns paypal payment data object
   *
   * @param type $router
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet 
   */
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
   * returns payment data for dbitnote payment
   *
   * @param array $paymentData
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment 
   */
  public function getPaymentDebitNote($paymentData)
  {
    $params = array();

    $params['bankcountry']       = $paymentData['mopt_payone__debit_bankcountry']; //DE, AT, NL
    $params['bankaccount']       = $this->removeWhitespaces($paymentData['mopt_payone__debit_bankaccount']);
    $params['bankcode']          = $this->removeWhitespaces($paymentData['mopt_payone__debit_bankcode']);
    $params['bankaccountholder'] = $paymentData['mopt_payone__debit_bankaccountholder']; //optional

    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment($params);
    return $payment;
  }

  /**
   * build payment data object for instant bank transfer payment methods
   *
   * @param object $router
   * @param array $paymentData
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_OnlineBankTransfer 
   */
  public function getPaymentInstantBankTransfer($router, $paymentData)
  {
    $params = array();

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PNT')
    {
      $params['onlinebanktransfertype'] = 'PNT';
      $params['bankcountry']            = $paymentData['mopt_payone__sofort_bankcountry']; //DE, AT, NL
      $params['bankaccount']            = $this->removeWhitespaces($paymentData['mopt_payone__sofort_bankaccount']);
      $params['bankcode']               = $this->removeWhitespaces($paymentData['mopt_payone__sofort_bankcode']);
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => true));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => true));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => true));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'GPY')
    {
      $params['onlinebanktransfertype'] = 'GPY';
      $params['bankcountry']            = $paymentData['mopt_payone__giropay_bankcountry']; //DE, AT, NL
      $params['bankaccount']            = $this->removeWhitespaces($paymentData['mopt_payone__giropay_bankaccount']);
      $params['bankcode']               = $this->removeWhitespaces($paymentData['mopt_payone__giropay_bankcode']);
      $params['successurl']             = $router->assemble(array('action'            => 'success', 'forceSecure'       => true, 'appendSession'     => false));
      $params['errorurl'] = $router->assemble(array('action'           => 'failure', 'forceSecure'      => true, 'appendSession'    => false));
      $params['backurl'] = $router->assemble(array('action'        => 'cancel', 'forceSecure'   => true, 'appendSession' => false));
    }

    if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'EPS')
    {
      $params['onlinebanktransfertype'] = 'EPS';
      $params['bankcountry']            = $this->removeWhitespaces($paymentData['mopt_payone__eps_bankcountry']);
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
   *
   * @param string $financeType
   * @param object $router
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing 
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
   * returns payment data for cash on delivery payment
   *
   * @param array $userData
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_CashOnDelivery 
   */
  public function getPaymentCashOnDelivery($userData)
  {
    $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CashOnDelivery();

    switch ($userData['additional']['countryShipping']['countryiso'])
    {
      case 'DE':
        {
          $payment->setShippingprovider(Payone_Api_Enum_Shippingprovider::DHL); // DE:DHL / IT:BRT
        }
        break;
      case 'IT':
        {
          $payment->setShippingprovider(Payone_Api_Enum_Shippingprovider::BARTOLINI); // DE:DHL / IT:Bartolini
        }

        break;
    }

    return $payment;
  }

  /**
   * returns payment data for credit card payment
   *
   * @param object $router
   * @param array $paymentData
   * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard 
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
   * returns business parameters
   * @TODO check if really needed, complete params are not mandatory
   *
   * @return \Payone_Api_Request_Parameter_Authorization_Business 
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
   *
   * @param array $basket
   * @param array $shipment
   * @return \Payone_Api_Request_Parameter_Invoicing_Transaction 
   */
  public function getInvoicing($basket, $shipment, $userData)
  {
    $params = array();
    $transaction = new Payone_Api_Request_Parameter_Invoicing_Transaction($params);

    $taxFree = false;
    if (isset($userData['additional']['charge_vat'])) {
      $taxFree = ! $userData['additional']['charge_vat'];
    }

    foreach ($basket['content'] as $article)
    {
      $params = array();

      $params['id'] = $article['ordernumber']; //article number
      $params['pr'] = $article['priceNumeric']; //price
      $params['no'] = $article['quantity']; // ordered quantity
      $params['de'] = substr($article['articlename'], 0, 100); // description
      $params['va'] = ($taxFree) ? 0 : number_format($article['tax_rate'], 0, '.', ''); // vat
      $params['it'] = Payone_Api_Enum_InvoicingItemType::GOODS; //item type
      if ($article['modus'] == 2)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
        $params['id'] = substr($article['articlename'], 0, 100);
      }
      if ($article['modus'] == 4)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
        $params['id'] = substr($article['articlename'], 0, 100);
      }
      $item         = new Payone_Api_Request_Parameter_Invoicing_Item($params);
      $transaction->addItem($item);
    }

    //add shipment as position
    $params = array();

    $params['id'] = substr($shipment['name'], 0, 100); //article number
    $params['pr'] = $basket['sShippingcosts']; //price
    $params['no'] = 1; // ordered quantity
    $params['de'] = substr($shipment['name'], 0, 100); // description check length
    $params['va'] = ($taxFree) ? 0 : number_format($basket['sShippingcostsTax'], 0, '.', ''); // vat
    $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;

    $params = array_map('utf8_encode', $params);

    $item = new Payone_Api_Request_Parameter_Invoicing_Item($params);
    $transaction->addItem($item);

    return $transaction;
  }

  /**
   * collect items from order
   *
   * @param object $order
   * @param array $positionIds
   * @param bool $finalize
   * @param bool $debit
   * @return \Payone_Api_Request_Parameter_Capture_Invoicing_Transaction 
   */
  public function getInvoicingFromOrder($order, $positionIds, $finalize = 'skipCaptureMode', $debit = false)
  {
    $params = array();
    $transaction = new Payone_Api_Request_Parameter_Capture_Invoicing_Transaction($params);


    foreach ($order->getDetails() as $position)
    {
      if (!in_array($position->getId(), $positionIds))
      {
        continue;
      }

      if (!$debit)
      {
        $positionAttribute = $this->payoneHelper->getOrCreateAttribute($position);
        if ($positionAttribute->getMoptPayoneCaptured())
        {
          continue;
        }
      }

      $params = array();

      $params['id'] = $position->getArticleNumber(); //article number
      $params['pr'] = $position->getPrice(); //price
      if ($debit)
      {
        $params['pr'] = $params['pr'] * -1;
      }
      $params['no'] = $position->getQuantity(); // ordered quantity
      $params['de'] = substr($position->getArticleName(), 0, 100); // description
      if ($order->getTaxFree())
      {
        $params['va'] = 0;
      }
      else if ($position->getTaxRate() == 0)
      {
        $params['va'] = number_format($position->getTax()->getTax(), 0, '.', '');
      }
      else
      {
        $params['va'] = number_format($position->getTaxRate(), 0, '.', ''); // vat
      }
      $params['it'] = Payone_Api_Enum_InvoicingItemType::GOODS; //item type
      $mode         = $position->getMode();
      if ($mode == 2)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
        $params['id'] = substr($position->getArticleName(), 0, 100); //article number
      }
      if ($mode == 4)
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
        $params['id'] = substr($position->getArticleName(), 0, 100); //article number
      }

      if ($position->getArticleNumber() == 'SHIPPING')
      {
        $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;
        $params['id'] = substr($position->getArticleName(), 0, 100); //article number
      }
      $params       = array_map('htmlspecialchars_decode', $params);
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
   * returns address check params
   *
   * @param array $addressFormData
   * @param array $personalFormData
   * @param string $paymentId
   * @return array 
   */
  public function getAddressCheckParams($addressFormData, $personalFormData, $paymentId = 0)
  {
    $params = array();
    $params = $this->getAuthParameters($paymentId);

    $params['firstname']    = $personalFormData['firstname'];
    $params['lastname']     = $personalFormData['lastname'];
    $params['company']      = $addressFormData['company'];
    $params['street']       = $addressFormData['street'] . ' ' . $addressFormData['streetnumber'];
    $params['streetname']   = $addressFormData['street'];
    $params['streetnumber'] = $addressFormData['streetnumber'];
    $params['zip']          = $addressFormData['zipcode'];
    $params['city']         = $addressFormData['city'];

    if (!empty($addressFormData['country']))
    {
      $params['country']  = $this->getCountryFromId($addressFormData['country']);
      $params['language'] = $this->getLanguageFromCountryId($addressFormData['country']);
    }
    if (isset($personalFormData['phone']))
    {
      $params['telephonenumber'] = $personalFormData['phone'];
    }

    return $params;
  }

  /**
   * returns consumerscore check params
   *
   * @param array $userFormData
   * @param string $paymentId
   * @return array 
   */
  public function getConsumerscoreCheckParams($userFormData, $paymentId = 0)
  {
    $params = $this->getAuthParameters($paymentId);

    $params['firstname']    = $userFormData['firstname'];
    $params['lastname']     = $userFormData['lastname'];
    $params['company']      = $userFormData['company'];
    $params['street']       = $userFormData['street'] . ' ' . $userFormData['streetnumber'];
    $params['streetname']   = $userFormData['street'];
    $params['streetnumber'] = $userFormData['streetnumber'];
    $params['zip']          = $userFormData['zipcode'];
    $params['city']         = $userFormData['city'];

    if (!empty($userFormData['countryID']))
    {
      $params['country']  = $this->getCountryFromId($userFormData['countryID']);
      $params['language'] = $this->getLanguageFromCountryId($userFormData['countryID']);
    }
    return $params;
  }

  /**
   * get country from id
   *
   * @param string $id
   * @return string 
   */
  protected function getCountryFromId($id)
  {
    $sql     = 'SELECT `countryiso` FROM s_core_countries WHERE id = ' . $id;
    $country = Shopware()->Db()->fetchOne($sql);
    return $country;
  }

  /**
   * get language from id
   *
   * @param string $id
   * @return string 
   */
  protected function getLanguageFromCountryId($id)
  {
    $sql      = 'SELECT `countryiso` FROM s_core_countries WHERE id = ' . $id;
    $language = Shopware()->Db()->fetchOne($sql);
    $language = strtolower($language);

    if ($language == 'at' || $language == 'ch' || $language == 'de')
    {
      $language = 'de';
    }
    else
    {
      $language = 'en';
    }

    return $language;
  }

  /**
   * get state from id
   *
   * @param string $id
   * @return string 
   */
  protected function getStateFromId($stateId, $countryIso)
  {
    $enabledTransmittingStatesCountryIsos = array('US', 'CA');

    if (!in_array($countryIso, $enabledTransmittingStatesCountryIsos))
    {
      return '';
    }

    $sql   = 'SELECT `shortcode` FROM s_core_countries_states WHERE id = ' . $stateId;
    $state = Shopware()->Db()->fetchOne($sql);

    return $state;
  }

  /**
   * create random payment reference
   *
   * @return string 
   */
  public function getParamPaymentReference()
  {
    return 'mopt_' . uniqid() . rand(10, 99);
  }

  /**
   * Remove whitespaces from input string
   *
   * @return string without whitespaces
   */
  private function removeWhitespaces($input)
  {
    return preg_replace('/\s+/', '', $input);
  }

}