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
   * @var MoptPayoneMain 
   */
  protected $moptPayoneMain = null;

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
    Shopware()->Template()->addTemplateDir(dirname(__FILE__) . '/Views/');

    $this->admin = Shopware()->Modules()->Admin();
    $this->payoneServiceBuilder = $this->Plugin()->Application()->PayoneBuilder();
    $this->moptPayoneMain = $this->Plugin()->Application()->PayoneMain();
  }

  /**
   * Pre dispatch method
   */
  public function preDispatch()
  {
    $this->View()->setScope(Enlight_Template_Manager::SCOPE_PARENT);
  }

  /**
   * check if the plugin payment methods are choosen
   * 
   * @return type 
   */
  public function indexAction()
  {
    $session = Shopware()->Session();

    if ($session->moptConsumerScoreCheckNeedsUserAgreement)
    {
      return $this->redirect(array('controller' => 'checkout'));
    }

    if (preg_match('#mopt_payone__cc#', $this->getPaymentShortName()))
    {
      return $this->redirect(array('action'      => 'creditcard', 'forceSecure' => true));
    }

    switch ($this->getPaymentShortName())
    {
      case 'mopt_payone_creditcard':
        return $this->redirect(array('action'      => 'creditcard', 'forceSecure' => true));
      case 'mopt_payone__ibt_sofortueberweisung':
        return $this->redirect(array('action'      => 'instanttransfer', 'forceSecure' => true));
      case 'mopt_payone__ibt_giropay':
        return $this->redirect(array('action'      => 'instanttransfer', 'forceSecure' => true));
      case 'mopt_payone__ibt_eps':
        return $this->redirect(array('action'      => 'instanttransfer', 'forceSecure' => true));
      case 'mopt_payone__ibt_post_efinance':
        return $this->redirect(array('action'      => 'instanttransfer', 'forceSecure' => true));
      case 'mopt_payone__ibt_post_finance_card':
        return $this->redirect(array('action'      => 'instanttransfer', 'forceSecure' => true));
      case 'mopt_payone__ibt_ideal':
        return $this->redirect(array('action'      => 'instanttransfer', 'forceSecure' => true));
      case 'mopt_payone__ewallet_paypal':
        return $this->redirect(array('action'      => 'paypal', 'forceSecure' => true));
      case 'mopt_payone__acc_debitnote':
        return $this->redirect(array('action'      => 'debitnote', 'forceSecure' => true));
      case 'mopt_payone__acc_invoice':
        return $this->redirect(array('action'      => 'standard', 'forceSecure' => true));
      case 'mopt_payone__acc_payinadvance':
        return $this->redirect(array('action'      => 'standard', 'forceSecure' => true));
      case 'mopt_payone__acc_cashondel':
        return $this->redirect(array('action'      => 'cashondel', 'forceSecure' => true));
      case 'mopt_payone__fin_billsafe':
        return $this->redirect(array('action'      => 'finance', 'forceSecure' => true));
      case 'mopt_payone__fin_commerzfin':
        return $this->redirect(array('action'      => 'finance', 'forceSecure' => true));

      default:
        return $this->redirect(array('controller' => 'checkout'));
    }
  }

  public function creditcardAction()
  {
    $response = $this->mopt_payone__creditcard();
    if ($response->getStatus() == "REDIRECT")
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

    $sql         = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
    $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentCreditCard($this->Front()->Router(), $paymentData);
    $response = $this->mopt_payone__buildAndCallPayment($config, 'cc', $payment);

    return $response;
  }

  /**
   * @return $response 
   */
  protected function mopt_payone__instanttransfer()
  {
    $userId = Shopware()->Session()->sUserId;

    $sql         = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
    $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

    switch ($this->getPaymentShortName())
    {
      case 'mopt_payone__ibt_sofortueberweisung':
        $paymentData['mopt_payone__onlinebanktransfertype'] = 'PNT';
        break;
      case 'mopt_payone__ibt_giropay':
        $paymentData['mopt_payone__onlinebanktransfertype'] = 'GPY';
        break;
      case 'mopt_payone__ibt_eps':
        $paymentData['mopt_payone__onlinebanktransfertype'] = 'EPS';
        break;
      case 'mopt_payone__ibt_post_efinance':
        $paymentData['mopt_payone__onlinebanktransfertype'] = 'PFF';
        break;
      case 'mopt_payone__ibt_post_finance_card':
        $paymentData['mopt_payone__onlinebanktransfertype'] = 'PFC';
        break;
      case 'mopt_payone__ibt_ideal':
        $paymentData['mopt_payone__onlinebanktransfertype'] = 'IDL';
        break;
    }

    $config   = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentInstantBankTransfer($this->Front()->Router(), $paymentData);
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

    if ($paymentId == 'mopt_payone__acc_invoice')
    {
      $clearingType = 'rec';
    }
    else
    {
      $clearingType = 'vor';
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
    $payment  = $this->moptPayoneMain->getParamBuilder()->getPaymentCashOnDelivery();
    $response = $this->mopt_payone__buildAndCallPayment($config, 'cod', $payment);

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
      $financeType = 'BSV';
    }
    else
    {
      $financeType = 'CFR';
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
    $this->mopt_payone_saveOrder($session->txId, $session->paymentReference);
  }

  /**
   * this action is submitted to Payone with redirect payments
   * url is called when customer payment fails on 3rd party site
   */
  public function paymentPendingAction()
  {
    $this->View()->moptPayoneMessage = "Die Bezahlung wurde von PAYONE noch nicht bestätigt";
  }

  /**
   * this action is submitted to Payone with redirect payments
   * url is called when customer payment fails on 3rd party site
   */
  public function failureAction()
  {
    $this->View()->errormessage = "Es ist ein Fehler aufgetreten";
    $this->forward('error');
  }

  /**
   * this action is submitted to Payone with redirect payments
   * url is called when customer cancels redirect payment on 3rd party site
   */
  public function cancelAction()
  {
    $this->View()->errormessage = "Der Bezahlvorgang wurde abgebrochen";
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
   * renders automatically error.tpl, errormessage is already assigne e.g. mopt_payone__handleDirectFeedback
   * @TODO check if it's possible to redirect to checkout and display error
   */
  public function errorAction()
  {
    
  }

  /**
   * acutally save order
   * @TODO implement to save all Payone details
   *
   * @param type $txId
   * @param type $hash
   * @return type 
   */
  protected function mopt_payone_saveOrder($txId, $hash)
  {
    $orderNr = $this->saveOrder($txId, $hash);
    $session = Shopware()->Session();

    //get order id
    $sql     = 'SELECT `id` FROM `s_order` WHERE ordernumber = ?';
    $orderId = Shopware()->Db()->fetchOne($sql, $orderNr);

    $sql = 'UPDATE `s_order_attributes`' .
            'SET mopt_payone_txid=?, mopt_payone_is_authorized=? WHERE orderID = ?';
    Shopware()->Db()->query($sql, array($txId, $session->moptIsAuthorized, $orderId));
    unset($session->moptIsAuthorized);

    return $this->forward('finish', 'checkout', null, array('sAGB'      => 1, 'sUniqueID' => $hash));
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
      $this->View()->errormessage = $response->getCustomermessage();
      $this->forward('error');
    }
    else
    {
      //save order
      $this->mopt_payone_saveOrder($response->getTxid(), $session->paymentReference);
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
      $this->View()->errormessage = $response->getCustomermessage();
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

    $session->paymentReference = $paymentReference;
    $session->shopwareTemporaryId = $shopwareTemporaryId;

    $personalData = $paramBuilder->getPersonalData($this->getUserData());
    $request->setPersonalData($personalData);
    $deliveryData = $paramBuilder->getDeliveryData($this->getUserData());
    $request->setDeliveryData($deliveryData);

    $request->setClearingtype($clearingType);

    if ($config['submitBasket'] || $clearingType === 'fnc')
    {
      $request->setInvoicing($paramBuilder->getInvoicing($this->getBasket()));
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
   * prepare creditcard registration layer form for client api call and creditcard registration
   * 
   * @TODO get correct config
   * 
   */
  public function ajaxCreditCardAction()
  {
    $config = $this->moptPayoneMain->getPayoneConfig();
    $userId = Shopware()->Session()->sUserId;

    $sql         = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
    $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

    $paymentMeans = $this->admin->sGetPaymentMeans();
    foreach ($paymentMeans as $paymentMean)
    {
      if ($paymentMean['id'] == 'mopt_payone_creditcard')
      {
        $paymentMean['mopt_payone_credit_cards'] = $this->moptPayoneMain->getHelper()->mapCardLetter($paymentMean['mopt_payone_credit_cards']);
        $this->View()->payment_mean = $paymentMean;
        break;
      }
    }

    $payoneParams             = $this->moptPayoneMain->getParamBuilder()->buildAuthorize();
    $payoneParams['aid']      = $config['subaccountId'];
    $payoneParams['language'] = 'de'; //@TODO get language


    $serviceGenerateHash = $this->payoneServiceBuilder->buildServiceClientApiGenerateHash();

    $request = new Payone_ClientApi_Request_CreditCardCheck();
    $params  = array(
        'aid'                => $payoneParams['aid'],
        'mid'                => $payoneParams['mid'],
        'portalid'           => $payoneParams['portalid'],
        'mode'               => $payoneParams['mode'],
        'encoding'           => 'UTF-8',
        'language'           => 'de', //@TODO get language
        'solution_version'   => '0.0.1',
        'solution_name'      => 'mediaopt',
        'integrator_version' => '4.0.5',
        'integrator_name'    => 'Shopware',
        'storecarddata'      => 'yes',
    );
    $request->init($params);
    $request->setResponsetype('JSON');

    $payoneParams['hash'] = $serviceGenerateHash->generate($request, $config['apiKey']);

//    if($config['checkCc'])

    $this->View()->moptPayoneCheckCc = $config['checkCc'];

    $this->View()->sFormData = $paymentData;
    $this->View()->moptPayoneParams = $payoneParams;
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
      $this->View()->consumerscoreNoteMessage = $config['consumerscoreNoteMessage'];
    }
    if ($config['consumerscoreAgreementActive'])
    {
      $this->View()->consumerscoreAgreementMessage = $config['consumerscoreAgreementMessage'];
    }

    unset($session->moptConsumerScoreCheckNeedsUserAgreement);
    unset($_SESSION['moptConsumerScoreCheckNeedsUserAgreement']);
  }

  public function checkConsumerScoreAction()
  {
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
    $params                        = $this->moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $paymentId);
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
        $this->moptPayoneMain->getHelper()->deletePaymentData($userId);
        $this->moptPayoneMain->getHelper()->setPayonePrepaymentAsPayment($userId);
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
      //delete payment data and set to p1 prepeyment
      $this->moptPayoneMain->getHelper()->deletePaymentData($userId);
      $this->moptPayoneMain->getHelper()->setPayonePrepaymentAsPayment($userId);
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
    $session = Shopware()->Session();

    $userId   = $session->sUserId;
    $response = unserialize($session->moptAddressCheckCorrectedAddress);
    $config   = $this->moptPayoneMain->getPayoneConfig();

    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringValue($response->getPersonstatus(), $config);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
    $this->moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);

    unset($session->moptAddressCheckNeedsUserVerification);
    unset($session->moptAddressCheckOriginalAddress);
    unset($session->moptAddressCheckCorrectedAddress);
  }

  public function saveCorrectedAddressAction()
  {
    $session  = Shopware()->Session();
    $userId   = $session->sUserId;
    $response = unserialize($session->moptAddressCheckCorrectedAddress);
    $config   = $this->moptPayoneMain->getPayoneConfig();

    $this->moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $response);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringValue($response->getPersonstatus(), $config);
    $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
    $this->moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);

    unset($session->moptAddressCheckNeedsUserVerification);
    unset($session->moptAddressCheckOriginalAddress);
    unset($session->moptAddressCheckCorrectedAddress);
  }

  /**
   * AJAX action called from creditcard layer, saves client api response
   */
  public function savePseudoCardAction()
  {
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

}