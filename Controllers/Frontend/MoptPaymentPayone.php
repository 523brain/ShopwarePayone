<?php

/**
 * $Id: $
 */

/**
 * mopt payone payment controller
 */
class Shopware_Controllers_Frontend_MoptPaymentPayone extends Shopware_Controllers_Frontend_Payment
{

  /**
   * Reference to sAdmin object (core/class/sAdmin.php)
   *
   * @var sAdmin
   */
  protected $admin;

  /**
   * PayoneMain
   * @var Mopt_PayoneMain 
   */
  protected $moptPayoneMain = null;

  /**
   * PayoneMain
   * @var Mopt_PayonePaymentHelper 
   */
  protected $moptPayonePaymentHelper = null;

  /**
   * PayOne Builder
   * @var PayoneBuilder 
   */
  protected $payoneServiceBuilder = null;
  protected $service              = null;

  /**
   * Init method that get called automatically
   *
   * Set class properties
   */
  public function init()
  {
    $this->Plugin()->setCorrectViewsFolder();

    $this->admin = Shopware()->Modules()->Admin();
    $this->payoneServiceBuilder = $this->Plugin()->Application()->PayoneBuilder();
    $this->moptPayoneMain = $this->Plugin()->Application()->PayoneMain();
    $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
  }

  /**
   * Pre dispatch method
   */
  public function preDispatch()
  {
    $this->View()->setScope(Enlight_Template_Manager::SCOPE_PARENT);
    
    if (in_array($this->Request()->getActionName(), array('recurring'))) {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    }
  }

  /**
   * check if the plugin payment methods are choosen
   * 
   * @return redirect to payment action or checkout controller 
   */
  public function indexAction()
  {
    $session = Shopware()->Session();
    
    if ($session->moptConsumerScoreCheckNeedsUserAgreement)
    {
      return $this->redirect(array('controller' => 'checkout'));
    }
    
    $action = $this->moptPayonePaymentHelper->getActionFromPaymentName($this->getPaymentShortName());


    if ($action === 'debitnote')
    {
      if($session->moptMandateData['mopt_payone__showMandateText'] == true && $session->moptMandateAgreement !== 'on') 
      {
        $session->moptMandateAgreementError = true;
        $action = false;
      }
    }
    
    if($action)
    {
        return $this->redirect(array('action' => $action, 'forceSecure' => true));
    }
    else
    {
        return $this->redirect(array('controller' => 'checkout'));
    }
  }

  public function creditcardAction()
  {
    $response = $this->mopt_payone__creditcard();
    if ($response->isRedirect())
    {
      $this->mopt_payone__handleRedirectFeedback($response);
    }
    else
    {
      $this->mopt_payone__handleDirectFeedback($response);
    }
  }

  public function instanttransferAction()
  {
    $response = $this->mopt_payone__instanttransfer();
    $this->mopt_payone__handleRedirectFeedback($response);
  }

  public function paypalAction()
  {
    $response = $this->mopt_payone__paypal();
    $this->mopt_payone__handleRedirectFeedback($response);
  }

  public function debitnoteAction()
  {
    $response = $this->mopt_payone__debitnote();
    $this->mopt_payone__handleDirectFeedback($response);
  }

  public function standardAction()
  {
    $response = $this->mopt_payone__standard();
    $this->mopt_payone__handleDirectFeedback($response);
  }

  public function cashondelAction()
  {
    $response = $this->mopt_payone__cashondel();
    $this->mopt_payone__handleDirectFeedback($response);
  }

  public function klarnaAction()
  {
    $response = $this->mopt_payone__klarna();
    $this->mopt_payone__handleDirectFeedback($response);
  }

  public function financeAction()
  {
    $response = $this->mopt_payone__finance();
    $this->mopt_payone__handleRedirectFeedback($response);
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__creditcard()
  {
    $userId = Shopware()->Session()->sUserId;

    if($this->isRecurringOrder())
    {
        $paymentData = Shopware()->Session()->moptPayment;
    }
    else
    {
        $sql         = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));
    }

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentCreditCard($this->Front()->Router(), $paymentData);
    if(Shopware()->Session()->moptOverwriteEcommerceMode)
    {
        $payment->setEcommercemode(Shopware()->Session()->moptOverwriteEcommerceMode);
        unset(Shopware()->Session()->moptOverwriteEcommerceMode);
    }
    $response = $this->mopt_payone__buildAndCallPayment($config, 'cc', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__instanttransfer()
  {
    $userId = Shopware()->Session()->sUserId;

    if($this->isRecurringOrder())
    {
        $paymentData = Shopware()->Session()->moptPayment;
    }
    else
    {
        $sql              = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData      = unserialize(Shopware()->Db()->fetchOne($sql, $userId));
    }
    $paymentShortName = $this->getPaymentShortName();

    $paymentData['mopt_payone__onlinebanktransfertype'] = $this->moptPayonePaymentHelper
            ->getOnlineBankTransferTypeFromPaymentName($paymentShortName);

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()
            ->getPaymentInstantBankTransfer($this->Front()->Router(), $paymentData);
    $response = $this->mopt_payone__buildAndCallPayment($config, 'sb', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__paypal()
  {
    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentPaypal($this->Front()->Router());
    $response = $this->mopt_payone__buildAndCallPayment($config, 'wlt', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__debitnote()
  {
    $paymentData = Shopware()->Session()->moptPayment;

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentDebitNote($paymentData);
    $response = $this->mopt_payone__buildAndCallPayment($config, 'elv', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__standard()
  {
    $paymentId = $this->getPaymentShortName();

    if ($this->moptPayonePaymentHelper->isPayoneInvoice($paymentId))
    {
      $clearingType = Payone_Enum_ClearingType::INVOICE;
    }
    else
    {
      $clearingType = Payone_Enum_ClearingType::ADVANCEPAYMENT;
    }

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $response = $this->mopt_payone__buildAndCallPayment($config, $clearingType, null);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__cashondel()
  {
    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentCashOnDelivery($this->getUserData());
    $response = $this->mopt_payone__buildAndCallPayment($config, 'cod', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__klarna()
  {
    if($this->moptPayonePaymentHelper->isPayoneKlarnaInstallment($this->getPaymentShortName()))
    {
        $financeType = Payone_Api_Enum_FinancingType::KLS;
    }
    else
    {
        $financeType = Payone_Api_Enum_FinancingType::KLV;
    }
      

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    
    if($config['klarnaCampaignCode'])
    {
      $campaignId = $config['klarnaCampaignCode'];
    }
    else
    {
      $campaignId = false;
    }
    
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentKlarna($financeType, $campaignId);
    
    $response = $this->mopt_payone__buildAndCallPayment($config, 'fnc', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__finance()
  {
    $paymentId = $this->getPaymentShortName();

    if ($paymentId == 'mopt_payone__fin_billsafe')
    {
      $financeType = Payone_Api_Enum_FinancingType::BSV;
    }
    else
    {
      $financeType = Payone_Api_Enum_FinancingType::CFR;
    }

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentFinance($financeType, $this->Front()->Router());
    $response = $this->mopt_payone__buildAndCallPayment($config, 'fnc', $payment);

    return $response;
  }

  /**
   * this action is submitted to Payone with redirect payments
   * url is called when customer payment succeeds on 3rd party site
   */
  public function successAction()
  {
    $session = Shopware()->Session();
    $this->forward('finishOrder', 'MoptPaymentPayone', null, 
            array('txid' => $session->txId, 'hash' => $session->paymentReference));
  }

  /**
   * this action is submitted to Payone with redirect payments
   * url is called when customer payment fails on 3rd party site
   */
  public function failureAction()
  {
    $this->View()->errormessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
            ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten', true);
    $this->forward('error');
  }

  /**
   * this action is submitted to Payone with redirect payments
   * url is called when customer cancels redirect payment on 3rd party site
   */
  public function cancelAction()
  {
    $this->View()->errormessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/messages')
            ->get('cancelMessage', 'Der Bezahlvorgang wurde abgebrochen', true);
    $this->forward('error');
  }

  /**
   * Returns the payment plugin config data.
   *
   * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
   */
  public function Plugin()
  {
    return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
  }

  /**
   * Cancel action method
   * renders automatically error.tpl, errormessage is already assigned e.g. mopt_payone__handleDirectFeedback
   * @TODO check if it's possible to redirect to checkout and display error
   */
  public function errorAction()
  {
  }

  /**
   * acutally save order
   *
   * @param type $txId
   * @param type $hash
   * @return type 
   */
  public function finishOrderAction()
  {
    $txId    = $this->Request()->getParam('txid');
    $hash    = $this->Request()->getParam('hash');
    $session = Shopware()->Session();

    $orderNr = $this->saveOrder($txId, $hash);

    if ($session->moptClearingData)
    {
      $clearingData = json_encode($session->moptClearingData);
      unset($session->moptClearingData);
    }

    //get order id
    $sql     = 'SELECT `id` FROM `s_order` WHERE ordernumber = ?';
    $orderId = Shopware()->Db()->fetchOne($sql, $orderNr);

    if ($clearingData)
    {
      $sql = 'UPDATE `s_order_attributes`' .
              'SET mopt_payone_txid=?, mopt_payone_is_authorized=?, mopt_payone_clearing_data=? WHERE orderID = ?';
      Shopware()->Db()->query($sql, array($txId, $session->moptIsAuthorized, $clearingData, $orderId));
    }
    else
    {
      $sql = 'UPDATE `s_order_attributes`' .
              'SET mopt_payone_txid=?, mopt_payone_is_authorized=? WHERE orderID = ?';
      Shopware()->Db()->query($sql, array($txId, $session->moptIsAuthorized, $orderId));
    }

    if(Shopware()->Session()->moptPayment)
    {
        $this->saveTransactionPaymentData($orderId, Shopware()->Session()->moptPayment);
    }
    
    
    unset($session->moptIsAuthorized);
    unset($session->moptAgbChecked);

    $this->redirect(array('controller' => 'checkout', 'action' => 'finish', 'sUniqueID'  => $hash));
  }

  /**
   * handle direct feedback
   * on success save order
   *
   * @param type $response 
   */
  protected function mopt_payone__handleDirectFeedback($response)
  {
    $session = Shopware()->Session();

    if ($response->getStatus() == 'ERROR')
    {
      $this->View()->errormessage = $this->moptPayoneMain->getPaymentHelper()
              ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
      $this->forward('error');
    }
    else
    {
      //extract possible clearing data
      $clearingData = $this->moptPayoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);

      if ($clearingData)
      {
        $session->moptClearingData = $clearingData;
      }
      
      if ($session->moptMandateData)
      {
        $session->moptMandateDataDownload = $session->moptMandateData['mopt_payone__mandateIdentification'];
        unset($session->moptMandateData);
      }

      //save order
      $this->forward('finishOrder', 'MoptPaymentPayone', null, array('txid' => $response->getTxid(), 
          'hash' => $session->paymentReference));
    }
  }

  /**
   * handles redirect feedback
   * on success redirect customer to submitted(from Pay1) redirect url
   *
   * @param type $response 
   */
  protected function mopt_payone__handleRedirectFeedback($response)
  {
    if ($response->getStatus() == 'ERROR')
    {
      $this->View()->errormessage = $this->moptPayoneMain->getPaymentHelper()
              ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
      $this->forward('error');
    }
    else
    {
      $session = Shopware()->Session();
      $session->txId = $response->getTxid();
      $session->txStatus = $response->getStatus();

      $shopwareTemporaryId = $this->admin->sSYSTEM->sSESSION_ID;

      //set txid
      $sql = 'UPDATE `s_order`' .
              'SET transactionID=? WHERE temporaryID = ?';
      Shopware()->Db()->query($sql, array($response->getTxid(), $shopwareTemporaryId));

      $this->redirect($response->getRedirecturl());
    }
  }

  /**
   * preare and do payment server api call
   *
   * @param type $config
   * @param type $clearingType
   * @param type $payment
   * @return type $response
   */
  protected function mopt_payone__buildAndCallPayment($config, $clearingType, $payment)
  {
    $paramBuilder = $this->moptPayoneMain->getParamBuilder();
    $session      = Shopware()->Session();
    
    if ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung')
    {
      $request = $this->mopt_payone__prepareRequestPreauthorize($config['paymentId']);
      $session->moptIsAuthorized = false;
    }
    else
    {
      $request = $this->mopt_payone__prepareRequestAuthorize($config['paymentId']);
      $session->moptIsAuthorized = true;
    }

    $request->setAmount($this->getAmount());
    $request->setCurrency($this->getCurrencyShortName());
    
    //get shopware temporary order id - session id
    $shopwareTemporaryId = $this->admin->sSYSTEM->sSESSION_ID;
    $paymentReference    = $paramBuilder->getParamPaymentReference();

    $request->setReference($paymentReference);
    $transactionStatusPushCustomParam = 'session-' . Shopware()->Shop()->getId() 
            . '|' . $this->admin->sSYSTEM->sSESSION_ID;
    $request->setParam($transactionStatusPushCustomParam);

    $session->paymentReference = $paymentReference;
    $session->shopwareTemporaryId = $shopwareTemporaryId;

    $personalData = $paramBuilder->getPersonalData($this->getUserData());
    $request->setPersonalData($personalData);
    $deliveryData = $paramBuilder->getDeliveryData($this->getUserData());
    $request->setDeliveryData($deliveryData);

    $request->setClearingtype($clearingType);

    if ($config['submitBasket'] || $clearingType === 'fnc')
    {
      $request->setInvoicing($paramBuilder->getInvoicing($this->getBasket(), $this->getShipment(), $this->getUserData()));
    }

    if ($payment)
    {
      $request->setPayment($payment);
    }

    if ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung')
    {
      $response = $this->service->preauthorize($request);
    }
    else
    {
      $response = $this->service->authorize($request);
    }

    return $response;
  }

  /**
   * initialize and return request object for authorize api call
   *
   * @return \Payone_Api_Request_Authorization 
   */
  protected function mopt_payone__prepareRequestAuthorize($paymentId = 0)
  {
    $params  = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($paymentId);
    $this->service = $this->payoneServiceBuilder->buildServicePaymentAuthorize();
    $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                    'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
            ));
    $request = new Payone_Api_Request_Authorization($params);

    return $request;
  }

  /**
   * initialize and return request object for preauthorize api call
   *
   * @return \Payone_Api_Request_Preauthorization 
   */
  protected function mopt_payone__prepareRequestPreauthorize($paymentId = 0)
  {
    $params  = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($paymentId);
    $this->service = $this->payoneServiceBuilder->buildServicePaymentPreauthorize();
    $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                    'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
            ));
    $request = new Payone_Api_Request_Preauthorization($params);

    return $request;
  }

  /**
   * Get complete user-data as an array to use in view
   *
   * @return array
   */
  public function getUserData()
  {
    if($this->isRecurringOrder())
    {
        $orderVars = Shopware()->Session()->sOrderVariables;
        return $orderVars['sUserData'];
    }
      
    $system   = Shopware()->System();
    $userData = $this->admin->sGetUserData();
    if (!empty($userData['additional']['countryShipping']))
    {
      $sTaxFree = false;
      if (!empty($userData['additional']['countryShipping']['taxfree']))
      {
        $sTaxFree = true;
      }
      elseif (
              !empty($userData['additional']['countryShipping']['taxfree_ustid'])
              && !empty($userData['billingaddress']['ustid'])
              && $userData['additional']['country']['id'] == $userData['additional']['countryShipping']['id']
      )
      {
        $sTaxFree = true;
      }

      $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow("
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ", array($system->sUSERGROUP));

      if (!empty($sTaxFree))
      {
        $system->sUSERGROUPDATA['tax'] = 0;
        $system->sCONFIG['sARTICLESOUTPUTNETTO'] = 1; //Old template
        Shopware()->Session()->sUserGroupData = $system->sUSERGROUPDATA;
        $userData['additional']['charge_vat'] = false;
        $userData['additional']['show_net']   = false;
        Shopware()->Session()->sOutputNet = true;
      }
      else
      {
        $userData['additional']['charge_vat'] = true;
        $userData['additional']['show_net']   = !empty($system->sUSERGROUPDATA['tax']);
        Shopware()->Session()->sOutputNet = empty($system->sUSERGROUPDATA['tax']);
      }
    }

    return $userData;
  }

  /**
   * ask user wether to keep original submittted or corrected values
   */
  public function ajaxVerifyAddressAction()
  {
    $session  = Shopware()->Session();
    $response = unserialize($session->moptAddressCheckCorrectedAddress);
    $this->View()->moptAddressCheckOriginalAddress = $session->moptAddressCheckOriginalAddress;
    $this->View()->moptAddressCheckCorrectedAddress = $response->toArray();

    if ($session->moptAddressCheckTarget)
    {
      $this->View()->moptAddressCheckTarget = $session->moptAddressCheckTarget;
    }
    else
    {
      $this->View()->moptAddressCheckTarget = 'checkout';
    }
  }

  /**
   * ask user wether to keep original submittted or corrected values
   */
  public function ajaxVerifyShippingAddressAction()
  {
    $session  = Shopware()->Session();
    $response = unserialize($session->moptShippingAddressCheckCorrectedAddress);
    $this->View()->moptShippingAddressCheckOriginalAddress = $session->moptShippingAddressCheckOriginalAddress;
    $this->View()->moptShippingAddressCheckCorrectedAddress = $response->toArray();

    if ($session->moptShippingAddressCheckTarget)
    {
      $this->View()->moptShippingAddressCheckTarget = $session->moptShippingAddressCheckTarget;
    }
    else
    {
      $this->View()->moptShippingAddressCheckTarget = 'checkout';
    }
  }

  /**
   * ask user wether to keep original submittted or newly chosen payment method
   */
  public function ajaxVerifyPaymentAction()
  {
    $this->View()->moptSelectedPayment = $this->Request()->getParam('moptSelectedPayment');
    $this->View()->moptOriginalPayment = $this->Request()->getParam('moptOriginalPayment');
    $this->View()->moptCheckedId = $this->Request()->getParam('moptCheckedId');
  }

  /**
   * ask user wether to keep original submittted or corrected values
   */
  public function ajaxGetConsumerScoreUserAgreementAction()
  {
    $session = Shopware()->Session();

    //get config
    if ($_SESSION['moptPaymentId'])
    {
      $paymentId = $_SESSION['moptPaymentId'];
    }
    else
    {
      $paymentId = $session->moptPaymentId;
    }

    $config = $this->moptPayoneMain->getPayoneConfig($paymentId);

    //add custom texts to view
    if ($config['consumerscoreNoteActive'])
    {
      $this->View()->consumerscoreNoteMessage = Shopware()->Snippets()
              ->getNamespace('frontend/MoptPaymentPayone/messages')
              ->get('consumerscoreNoteMessage');
    }
    if ($config['consumerscoreAgreementActive'])
    {
      $this->View()->consumerscoreAgreementMessage = Shopware()->Snippets()
              ->getNamespace('frontend/MoptPaymentPayone/messages')
              ->get('consumerscoreAgreementMessage');
    }

    unset($session->moptConsumerScoreCheckNeedsUserAgreement);
    unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);
  }

  public function checkConsumerScoreAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $session = Shopware()->Session();
    $userId  = $session->sUserId;

    unset($session->moptConsumerScoreCheckNeedsUserAgreement);
    unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);

    //get config
    if ($_SESSION['moptPaymentId'])
    {
      $paymentId = $_SESSION['moptPaymentId'];
    }
    else
    {
      $paymentId = $session->moptPaymentId;
    }

    //get payment data
    if ($_SESSION['moptPaymentData'])
    {
      $paymentData = $_SESSION['moptPaymentData'];
    }
    else
    {
      $paymentData = $session->moptPaymentData;
    }

    $config                        = $this->moptPayoneMain->getPayoneConfig($paymentId);
    $user                          = Shopware()->Modules()->Admin()->sGetUserData();
    $billingAddressData            = $user['billingaddress'];
    $billingAddressData['country'] = $billingAddressData['countryID'];
    //perform consumerscorecheck
    $params                        = $this->moptPayoneMain->getParamBuilder()
            ->getConsumerscoreCheckParams($billingAddressData, $paymentId);
    $service                       = $this->payoneServiceBuilder->buildServiceVerificationConsumerscore();
    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                    'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
            ));

    $request = new Payone_Api_Request_Consumerscore($params);

    $billingAddressChecktype = 'NO';
    $request->setAddresschecktype($billingAddressChecktype);
    $request->setConsumerscoretype($config['consumerscoreCheckMode']);

    $response = $service->score($request);

    if ($response->getStatus() == 'VALID')
    {
      //save result
      $this->moptPayoneMain->getHelper()->saveConsumerScoreCheckResult($userId, $response);
      unset($session->moptConsumerScoreCheckNeedsUserAgreement);
      unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);
      unset($session->moptPaymentId);
      echo json_encode(true);
    }
    else
    {
      //save error
      $this->moptPayoneMain->getHelper()->saveConsumerScoreError($userId, $response);
      unset($session->moptConsumerScoreCheckNeedsUserAgreement);
      unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);
      unset($session->moptPaymentId);
      //choose next action according to config
      if ($config['consumerscoreFailureHandling'] == 0)
      {
        //abort
        //delete payment data and set to payone prepayment
        $this->moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
        $this->moptPayoneMain->getPaymentHelper()->setConfiguredDefaultPaymentAsPayment($userId);
        echo json_encode(false);
      }
      else
      {
        //proceed 
        echo json_encode(true);
      }
    }
  }

  public function doNotCheckConsumerScoreAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $session = Shopware()->Session();

    unset($session->moptConsumerScoreCheckNeedsUserAgreement);
    unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);

    $userId = $session->sUserId;
    $config = $this->moptPayoneMain->getPayoneConfig($session->moptPaymentId);

    $this->moptPayoneMain->getHelper()->saveConsumerScoreDenied($userId);

    unset($session->moptConsumerScoreCheckNeedsUserAgreement);
    unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);
    unset($session->moptPaymentId);

    if ($config['consumerscoreFailureHandling'] == 0)
    {
      //abort
      //delete payment data and set to p1 prepayment
      $this->moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
      $this->moptPayoneMain->getPaymentHelper()->setConfiguredDefaultPaymentAsPayment($userId);
      echo json_encode(false);
    }
    else
    {
      //proceed
      echo json_encode(true);
    }
  }

  public function saveOriginalAddressAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $session = Shopware()->Session();

    $userId   = $session->sUserId;
    $response = unserialize($session->moptAddressCheckCorrectedAddress);
    $config   = $this->moptPayoneMain->getPayoneConfig();

    $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
    $this->moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);

    unset($session->moptAddressCheckNeedsUserVerification);
    unset($session->moptAddressCheckOriginalAddress);
    unset($session->moptAddressCheckCorrectedAddress);
  }

  public function saveCorrectedAddressAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $session  = Shopware()->Session();
    $userId   = $session->sUserId;
    $response = unserialize($session->moptAddressCheckCorrectedAddress);
    $config   = $this->moptPayoneMain->getPayoneConfig();

    $this->moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $response);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
    $this->moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);

    unset($session->moptAddressCheckNeedsUserVerification);
    unset($session->moptAddressCheckOriginalAddress);
    unset($session->moptAddressCheckCorrectedAddress);
  }

  public function saveOriginalShippingAddressAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $session = Shopware()->Session();

    $userId   = $session->sUserId;
    $response = unserialize($session->moptShippingAddressCheckCorrectedAddress);
    $config   = $this->moptPayoneMain->getPayoneConfig();

    $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
    $this->moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);

    unset($session->moptShippingAddressCheckNeedsUserVerification);
    unset($session->moptShippingAddressCheckOriginalAddress);
    unset($session->moptShippingAddressCheckCorrectedAddress);
  }

  public function saveCorrectedShippingAddressAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $session  = Shopware()->Session();
    $userId   = $session->sUserId;
    $response = unserialize($session->moptShippingAddressCheckCorrectedAddress);
    $config   = $this->moptPayoneMain->getPayoneConfig();

    $this->moptPayoneMain->getHelper()->saveCorrectedShippingAddress($userId, $response);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
    $this->moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);

    unset($session->moptShippingAddressCheckNeedsUserVerification);
    unset($session->moptShippingAddressCheckOriginalAddress);
    unset($session->moptShippingAddressCheckCorrectedAddress);
  }

  /**
   * AJAX action called from creditcard layer, saves client api response
   */
  public function savePseudoCardAction()
  {
    $this->Front()->Plugins()->ViewRenderer()->setNoRender();
    $userId = Shopware()->Session()->sUserId;

    $paymentData['mopt_payone__cc_truncatedcardpan']   = $this->Request()->getPost('mopt_payone__cc_truncatedcardpan');
    $paymentData['mopt_payone__cc_pseudocardpan']      = $this->Request()->getPost('mopt_payone__cc_pseudocardpan');
    $paymentData['mopt_payone__cc_cardtype']           = $this->Request()->getPost('mopt_payone__cc_cardtype');
    $paymentData['mopt_payone__cc_accountholder']      = $this->Request()->getPost('mopt_payone__cc_accountholder');
    $paymentData['mopt_payone__cc_month']              = $this->Request()->getPost('mopt_payone__cc_month');
    $paymentData['mopt_payone__cc_year']               = $this->Request()->getPost('mopt_payone__cc_year');
    $paymentData['mopt_payone__cc_paymentname']        = $this->Request()->getPost('mopt_payone__cc_paymentname');
    $paymentData['mopt_payone__cc_paymentid']          = $this->Request()->getPost('mopt_payone__cc_paymentid');
    $paymentData['mopt_payone__cc_paymentdescription'] = $this->Request()->getPost('mopt_payone__cc_paymentdescription');


    $actualPaymentId = $paymentData['mopt_payone__cc_paymentid'];

    $sql         = 'replace into `s_plugin_mopt_payone_payment_data`' .
            '(`userId`,`moptPaymentData`) values (?,?)';
    $paymentData = serialize($paymentData);
    Shopware()->Db()->query($sql, array($userId, $paymentData));

    $previousPayment = $this->admin->sGetUserData();
    $previousPayment = $previousPayment['additional']['user']['paymentID'];

    $previousPayment = $this->admin->sGetPaymentMeanById($previousPayment);

    if ($previousPayment['paymentTable'])
    {
      $deleteSQL = 'DELETE FROM ' . $previousPayment['paymentTable'] . ' WHERE userID=?';
      Shopware()->Db()->query($deleteSQL, array(Shopware()->Session()->sUserId));
    }

    $sqlPayment = "UPDATE s_user SET paymentID = ? WHERE id = ?";
    Shopware()->Db()->query($sqlPayment, array($actualPaymentId, $userId));
  }

  protected function getPaymentId()
  {
    $user = $this->getUser();
    return $user['additional']['payment']['id'];
  }

  /**
   * Returns the full basket data as array
   *
   * @return array
   */
  public function getBasket()
  {
    if (!empty(Shopware()->Session()->sOrderVariables['sBasket']))
    {
      return Shopware()->Session()->sOrderVariables['sBasket'];
    }
    else
    {
      return null;
    }
  }

  /**
   * Returns the full basket data as array
   *
   * @return array
   */
  public function getShipment()
  {
    if (!empty(Shopware()->Session()->sOrderVariables['sDispatch']))
    {
      return Shopware()->Session()->sOrderVariables['sDispatch'];
    }
    else
    {
      return null;
    }
  }

  /**
   * download SEPA mandate PDF file on success page
   * 
   * @return mixed
   */
  public function downloadMandateAction()
  {
    if(!Shopware()->Session()->moptMandateDataDownload)
    {
      $this->forward('downloadError');
      return;
    }

    $params  = $this->moptPayoneMain->getParamBuilder()->buildGetFile($this->getPaymentId(), 
            Shopware()->Session()->moptMandateDataDownload);
    $service = $this->payoneServiceBuilder->buildServiceManagementGetFile();
    $request = new Payone_Api_Request_GetFile($params);
    
    try
    {
      $response = $service->getFile($request);
      $this->Front()->Plugins()->ViewRenderer()->setNoRender();

      $httpResponse = $this->Response();
      $httpResponse->setHeader('Cache-Control', 'public');
      $httpResponse->setHeader('Content-Description', 'File Transfer');
      $httpResponse->setHeader('Content-disposition', 'attachment; filename=' . "Payone_Mandate.pdf");
      $httpResponse->setHeader('Content-Type', 'application/pdf');
      $httpResponse->setHeader('Content-Transfer-Encoding', 'binary');
      $httpResponse->setHeader('Content-Length', strlen($response->getRawResponse()));
      echo $response->getRawResponse();
    }
    catch (Exception $exc)
    {
      $this->forward('downloadError');
    }
  }
  
  /**
   *  this action is called when sth. goes wrong during SEPA mandate PDF download
   */
  public function downloadErrorAction()
  {
    $this->View()->errormessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
        ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten');
  }
  
    /**
     * Recurring payment action method.
     */
    public function recurringAction()
    {
        if (!$this->getAmount() || $this->getOrderNumber())
        {
            $this->redirect(array(
                'controller' => 'checkout'
            ));
            return;
        }

        $orderId                           = $this->Request()->getParam('orderId');
        Shopware()->Session()->moptPayment = $this->getPaymentDataFromOrder($orderId);
        
        if($this->moptPayonePaymentHelper->isPayoneCreditcard($this->getPaymentShortName()))
        {
            Shopware()->Session()->moptOverwriteEcommerceMode = Payone_Api_Enum_Ecommercemode::INTERNET;
        }
        
        $action                            = 'mopt_payone__' . $this->moptPayonePaymentHelper
                ->getActionFromPaymentName($this->getPaymentShortName());

        $response     = $this->$action();
        $errorMessage = false;
        if ($response->isRedirect())
        {
            $errorMessage = 'Tried to use redirect payment for abo order';
        }

        $session = Shopware()->Session();

        if ($response->getStatus() == 'ERROR')
        {
            $errorMessage = $response->getErrorcode();
        }

        if(!$errorMessage)
        {
            //extract possible clearing data
            $clearingData = $this->moptPayoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);
            
            //save order
            $orderNr = $this->saveOrder($response->getTxid(), $session->paymentReference);
                                        
            //get order id
            $sql     = 'SELECT `id` FROM `s_order` WHERE ordernumber = ?';
            $orderId = Shopware()->Db()->fetchOne($sql, $orderNr);

            if ($clearingData)
            {
                $sql = 'UPDATE `s_order_attributes`' .
                        'SET mopt_payone_txid=?, mopt_payone_is_authorized=?, '
                        . 'mopt_payone_clearing_data=? WHERE orderID = ?';
                Shopware()->Db()->query($sql, array($response->getTxid(), 
                    $session->moptIsAuthorized, json_encode($clearingData), $orderId));
            }
            else
            {
                $sql = 'UPDATE `s_order_attributes`' .
                        'SET mopt_payone_txid=?, mopt_payone_is_authorized=? WHERE orderID = ?';
                Shopware()->Db()->query($sql, array($response->getTxid(), $session->moptIsAuthorized, $orderId));
            }

            if (Shopware()->Session()->moptPayment)
            {
                $this->saveTransactionPaymentData($orderId, Shopware()->Session()->moptPayment);
            }

            unset($session->moptPayment);
            unset($session->moptIsAuthorized);
        }

        if ($this->Request()->isXmlHttpRequest())
        {
            if ($errorMessage)
            {
                $data = array(
                    'success' => false,
                    'message' => $errorMessage
                );
            }
            else
            {
                $data = array(
                    'success' => true,
                    'data'    => array(array(
                            'orderNumber'   => $orderNr,
                            'transactionId' => $response->getTxid(),
                        ))
                );
            }
            echo Zend_Json::encode($data);
        }
        else
        {
            if ($errorMessage)
            {
                $this->View()->errormessage = $this->moptPayoneMain->getPaymentHelper()
                        ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error');
            }
            else
            {
                $this->redirect(array(
                    'controller' => 'checkout',
                    'action'     => 'finish',
                    'sUniqueID'  => $session->paymentReference
                ));
            }
        }
    }
    
    /**
     * save payment data from actual transaction, used for abo commerce
     * 
     * @param string $orderId
     * @param array $paymentData
     */
    protected function saveTransactionPaymentData($orderId, $paymentData)
    {
        $sql = 'UPDATE `s_order_attributes` SET mopt_payone_payment_data=? WHERE orderID = ?';
        Shopware()->Db()->query($sql, array(serialize($paymentData), $orderId));
    }
    
    /**
     * get payment data from order, used for abo commerce
     * 
     * @param string $orderId
     * @return array
     */
    protected function getPaymentDataFromOrder($orderId)
    {
        $sql     = 'SELECT `mopt_payone_payment_data` FROM `s_order_attributes` WHERE orderID = ?';
        $paymentData = Shopware()->Db()->fetchOne($sql, $orderId);
        
        return unserialize($paymentData);
    }
    
    protected function isRecurringOrder()
    {
        return isset(Shopware()->Session()->isRecuringAboOrder);
    }
}
