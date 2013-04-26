<?php

/**
 * this class configures:
 * installment, uninstallment, updates, hooks, events, payment methods
 * 
 * @version $Id: $ 
 */
class Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

  /**
   * PayoneHelper
   * @var MoptPayoneHelper
   */
  protected $helper = null;

  /**
   * The afterInit function registers the custom plugin models.
   */
  public function afterInit()
  {
    $this->registerCustomModels();
  }

  /**
   * perform all neccessary install tasks and return true if successful
   *
   * @return boolean 
   */
  public function install()
  {
    $this->Application()->Loader()->registerNamespace(
            'Mopt', $this->Path() . 'Components/Classes/'
    );
    $this->helper = new Mopt_PayoneHelper();

    $this->createEvents();
    $this->createPayments();
    $this->createDatabase();
    $this->addAttributes();
    $this->createMenu();

    return array('success'         => true, 'invalidateCache' => array('backend', 'proxy'));
  }

  /**
   * @TODO remove comments, check if uninstall is necessary
   * perform all neccessary uninstall tasks and return true if successful
   *
   * @return boolean 
   */
  public function uninstall()
  {
    $em   = $this->Application()->Models();
    $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
//     
//     $classes = array(
//     $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog'),
//     );
//     $tool->dropSchema($classes);
//     
//     $classes = array(
//     $em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'),
//     );
//     $tool->dropSchema($classes);
//     
     $classes = array(
        $em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'),
    );
    $tool->dropSchema($classes);

    return true;
  }

  /**
   * @param type $oldVersion 
   */
  public function update($oldVersion)
  {
    switch ($oldVersion)
    {
      case '1.0.0' :
// Things to do to update a version 1.0.0 to the current version
        break;
    }
  }

  /**
   * @return boolean 
   */
  public function enable()
  {
    return true;
  }

  /**
   * @return boolean 
   */
  public function disable()
  {
    return true;
  }

  public function getCapabilities()
  {
    return array(
        'install' => true,
        'update'  => false,
        'enable'  => true
    );
  }

  /**
   * Returns the informations of plugin as array.
   *
   * @return array
   */
  public function getInfo()
  {
    $img = base64_encode(file_get_contents(dirname(__FILE__) . '/logo.png'));

    return array(
        'version'     => $this->getVersion(),
        'author'      => 'derksen mediaopt GmbH',
        'label'       => $this->getLabel(),
        'description' => '<p><img src="data:image/png;base64,' . $img . '" /></p> <p style="font-size:12px; font-weight: bold;">Pay save and secured through our payment service. For more information visit <a href="http://www.mediaopt.de">Mediaopt</a></p>',
        'copyright'   => 'Copyright © 2012, mediaopt',
        'support'     => 'shopware@mediaopt.de',
        'link'        => 'http://www.mediaopt.de/'
    );
  }

  /**
   * Returns the version of plugin as string.
   *
   * @return string
   */
  public function getVersion()
  {
    return '0.0.1';
  }

  public function getLabel()
  {
    return 'PAYONE Payment Plugin';
  }

  /**
   * register evenets and hooks
   */
  protected function createEvents()
  {
    $this->moptRegisterControllers();

    $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch', 'onPostDispatch', 110);

    $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_PayoneBuilder', 'onInitResourcePayoneBuilder'
    );

    $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_PayoneMain', 'onInitResourcePayoneMain'
    );

//risk management:Backend options
    $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_RiskManagement', 'onBackendRiskManagementPostDispatch'
    );

//risk management:Frontend extend sAdmin - implement sRisk<MOPT_RISK> methods called by sManageRisks
    $this->subscribeEvent('sAdmin::sManageRisks::replace', 'sAdmin__sManageRisks');

// group creditcard payments
    $this->subscribeEvent(
            'sAdmin::sGetPaymentMeans::after', 'onGetPaymentMeans'
    );

// hook for addresscheck
    $this->subscribeEvent(
            'sAdmin::sValidateStep2::after', 'onValidateStep2'
    );

// hook for saving addresscheck result
    $this->subscribeEvent(
            'sAdmin::sUpdateBilling::after', 'onUpdateBilling'
    );

    // hook for saving addresscheck result during registration process
    $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Register::saveRegister::after', 'onUpdateBilling'
    );

// hook for shipmentaddresscheck
    $this->subscribeEvent(
            'sAdmin::sValidateStep2ShippingAddress::after', 'onValidateStep2ShippingAddress'
    );

// hook for saving shipmentaddresscheck result
    $this->subscribeEvent(
            'sAdmin::sUpdateShipping::after', 'onUpdateShipping'
    );

    // hook for saving shipping ddresscheck result during registration process
    $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Register::saveRegister::after', 'onUpdateShipping'
    );

// save paymentdata
    $this->subscribeEvent(
            'sAdmin::sValidateStep3::after', 'onValidateStep3'
    );

// hook for saving consumerscorecheck result
    $this->subscribeEvent(
            'sAdmin::sUpdatePayment::after', 'onUpdatePayment'
    );

// load payment data from db to use for payment
    $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Account::paymentAction::after', 'onPaymentAction'
    );

// load stored payment data for payment method overview
    $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::getSelectedPayment::after', 'onGetSelectedPayment'
    );

// check if addresscheck and consumerscore are valid if activated
    $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::confirmAction::after', 'onConfirmAction'
    );

// extend backend order-overview
    $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Backend_Order', 'moptExtendController_Backend_Order');

    //add payone fields to list results
    $this->subscribeEvent('Shopware_Controllers_Backend_Order::getList::after', 'Order__getList__after');

    //remap payone order state after saveOrder
    $this->subscribeEvent('sOrder::sSaveOrder::after', 'sOrder__sSaveOrder__after');

    //copy attributes from temp-order
    $this->subscribeEvent('Shopware_Modules_Order_SaveOrderAttributes_FilterSQL', 'event_Shopware_Modules_Order_SaveOrderAttributes_FilterSQL');
  }

  protected function moptRegisterControllers()
  {
//Frontend
    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptPaymentPayone', 'onGetControllerPathFrontendMoptPaymentPayone');

    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_MoptShopNotification', 'moptRegisterController_Frontend_MoptShopNotification');

//Backend
    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPaymentPayone', 'onGetControllerPathBackend');

    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptConfigPayone', 'onGetConfigControllerBackend');

    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptApilogPayone', 'onGetApilogControllerBackend');

    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneTransactionLog', 'onGetTransactionLogControllerBackend');

    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptSupportPayone', 'onGetSupportControllerBackend');

    $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_MoptPayoneOrder', 'moptRegisterController_Backend_MoptPayoneOrder');
  }

  public function moptRegisterController_Backend_MoptPayoneOrder()
  {
    return $this->Path() . 'Controllers/Backend/MoptPayoneOrder.php';
  }

  public function moptRegisterController_Frontend_MoptShopNotification()
  {
    return $this->Path() . 'Controllers/Frontend/MoptShopNotification.php';
  }

  /**
   * complete replacement - refactor when shopware has a better implementation
   * @parent-arguments: $paymentID, $basket, $user
   * maybe: generally call parent for non-payone paymentIDs
   * 
   * @param Enlight_Hook_HookArgs $arguments
   * @return boolean
   */
  public function sAdmin__sManageRisks(Enlight_Hook_HookArgs $arguments)
  {
    $me = $arguments->getSubject();



    $paymentID      = $arguments->get('paymentID');
    $basket         = $arguments->get('basket');
    $user           = $arguments->get('user');
    $moptPayoneMain = $this->Application()->PayoneMain();
    $paymentName    = $moptPayoneMain->getHelper()->getPaymentNameFromId($paymentID);

    if (preg_match('#mopt_payone__#', $paymentName))
    {
      $isPayoneMethod = true;
    }
    else
    {
      $isPayoneMethod = false;
    }

    // disable consumerscore check if user is not logged in
    // important, cause payments are already loaded at addArticle (to basket) call
    if (!$user['additional']['user']['id'])
    {
      $isPayoneMethod = false;
    }

    //perform consumerScoreCheck if configured
    $config = $moptPayoneMain->getPayoneConfig($paymentID);
    
    if ($isPayoneMethod && $config['consumerscoreActive'] && $config['consumerscoreCheckMoment'] == 0)
    {
      //get user data
      $userData = $user['additional']['user'];

      $billingAddressData = $user['billingaddress'];

      if ($isPayoneMethod)
      {
        $userCconsumerScoreData = $moptPayoneMain->getHelper()->getConsumerScoreDataFromUserId($user['additional']['user']['id']);
      }

      if ($isPayoneMethod && !$moptPayoneMain->getHelper()->isCosumerScoreCheckValid($config['consumerscoreLifetime'], $userCconsumerScoreData['moptPayoneConsumerscoreResult'], $userCconsumerScoreData['moptPayoneConsumerscoreDate']))
      {
        $params                                    = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $paymentID);
        $response                                  = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
        //set result to userdata
        $userData['moptPayoneConsumerscoreResult'] = $response->getStatus();
        $userData['moptPayoneConsumerscoreDate']   = date('Y-m-d');

        if (!$this->handleConsumerScoreCheckResult($response, $config, $userData['id']))
        {
          //abort
          $arguments->setReturn(false);
          return;
        }
      }
    }

// Get all assigned rules
    $queryRules = $me->sSYSTEM->sDB_CONNECTION->GetAll("
            SELECT rule1, value1, rule2, value2
            FROM s_core_rulesets
            WHERE paymentID = ?
            ORDER BY id ASC
		", array($paymentID));

    if (empty($queryRules))
    {
      $result = $me->executeParent('sManageRisks', array($paymentID, $basket, $user));
      $arguments->setReturn($result);
      return;
    }

// Get-User-Data
// Get Basket
    if (empty($basket))
    {
      $session = Shopware()->Session();
      $basket  = array(
          'content'       => $session->sBasketQuantity,
          'AmountNumeric' => $session->sBasketAmount
      );
    }

    foreach ($queryRules as $rule)
    {
      if ($rule["rule1"] && !$rule["rule2"])
      {
        $rule["rule1"] = "sRisk" . $rule["rule1"];

        if (strpos($rule["rule1"], 'sRiskMOPT_PAYONE__') === 0)
        {
          if ($this->$rule["rule1"]($user, $basket, $rule["value1"], $paymentID))
          {
            $arguments->setReturn(true);
            return;
          }
        }
        elseif ($me->$rule["rule1"]($user, $basket, $rule["value1"]))
        {
          $arguments->setReturn(true);
          return;
        }
      }
      elseif ($rule["rule1"] && $rule["rule2"])
      {
        $rule["rule1"] = "sRisk" . $rule["rule1"];
        $rule["rule2"] = "sRisk" . $rule["rule2"];

//mopt
        if (strpos($rule["rule1"], 'sRiskMOPT_PAYONE__') === 0)
        {
          $result1 = $this->$rule["rule1"]($user, $basket, $rule["value1"], $paymentID);
        }
        else
        {
          $result1 = $me->$rule["rule1"]($user, $basket, $rule["value1"]);
        }

        if (strpos($rule["rule2"], 'sRiskMOPT_PAYONE__') === 0)
        {
          $result2 = $this->$rule["rule2"]($user, $basket, $rule["value2"], $paymentID);
        }
        else
        {
          $result2 = $me->$rule["rule2"]($user, $basket, $rule["value2"]);
        }

        // AND
        if ($result1 && $result2)
        {
          $arguments->setReturn(true);
          return;
        }
        else
        {
          $arguments->setReturn(false);
          return;
        }
      }
    }
  }

  /**
   * @parent-arguments $user,$order,$value
   * 
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS($user, $order, $value, $paymentID)
  {
    $payoneMain = $this->Application()->PayoneMain();
    $config     = $payoneMain->getPayoneConfig($paymentID);
    $scoring    = $payoneMain->getHelper()->getScoreFromUserAccordingToPaymentConfig($user, $config);

    return $scoring == $value; //return true if payment has to be denied
  }

  /**
   * @parent-arguments $user,$order,$value
   * 
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function sRiskMOPT_PAYONE__TRAFFIC_LIGHT_IS_NOT($user, $order, $value, $paymentID)
  {
    $payoneMain = $this->Application()->PayoneMain();
    $config     = $payoneMain->getPayoneConfig($paymentID);
    $scoring    = $payoneMain->getHelper()->getScoreFromUserAccordingToPaymentConfig($user, $config);

    return $scoring != $value;
  }

  /**
   * 
   * group creditcard payments
   * 
   * @TODO integrate comsumerscore check before choice of payment method
   * 
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function onGetPaymentMeans(Enlight_Hook_HookArgs $arguments)
  {
    $ret = $arguments->getReturn();

    $firstHit       = 'not_set';
    $creditCardData = array();

    foreach ($ret as $key => $paymentmean)
    {
      if (preg_match('#mopt_payone__cc#', $paymentmean['name']))
      {
        if ($firstHit === 'not_set')
        {
          $firstHit = $key;
        }

        $creditCard = array();
        $creditCard['id']          = $paymentmean['id'];
        $creditCard['name']        = $paymentmean['name'];
        $creditCard['description'] = $paymentmean['description'];

        $creditCardData[] = $creditCard;

        if ($firstHit != $key)
        {
          unset($ret[$key]);
        }
      }
    }

    // don't assign anything if no creditcard was found
    if ($firstHit === 'not_set')
    {
      $arguments->setReturn($ret);
    }

    $ret[$firstHit]['id']                       = 'mopt_payone_creditcard';
    $ret[$firstHit]['name']                     = 'mopt_payone_creditcard';
    $ret[$firstHit]['description']              = 'Kreditkarte';
    $ret[$firstHit]['mopt_payone_credit_cards'] = $creditCardData;

    $arguments->setReturn($ret);
  }

  public function onPaymentAction(Enlight_Hook_HookArgs $arguments)
  {
    $caller = $arguments->getSubject();
    $userId = Shopware()->Session()->sUserId;

    $sql         = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
    $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

    if (!empty($paymentData))
    {
      //get array of creditcard payment ids
      $sql           = "SELECT `id` FROM s_core_paymentmeans WHERE name LIKE '%mopt_payone__cc_%' ";
      $creditcardIds = Shopware()->Db()->fetchAll($sql);

      foreach ($creditcardIds as $creditcardId)
      {
        // check if active id is in array
        if ($creditcardId['id'] == $caller->View()->sFormData['payment'])
        {
          // set creditcard active
          $paymentData['payment'] = 'mopt_payone_creditcard';
          $caller->View()->sFormData = $paymentData;
          break;
        }
        else
        {
          $caller->View()->sFormData += $paymentData;
        }
      }
    }
  }

  public function onGetSelectedPayment(Enlight_Hook_HookArgs $arguments)
  {
    $ret     = $arguments->getReturn();
    $subject = $arguments->getSubject();
    $userId  = Shopware()->Session()->sUserId;

    $sql         = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
    $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

    $ret['data'] = $paymentData;

    //special handling for creditCards
    if (preg_match('#mopt_payone__cc#', $ret['name']))
    {
      $ret['id'] = 'mopt_payone_creditcard';
    }

    $arguments->setReturn($ret);
  }

  public function onConfirmAction(Enlight_Hook_HookArgs $arguments)
  {
    $subject = $arguments->getSubject();
    //return if non payone method is choosen
    if (!preg_match('#mopt_payone__#', $subject->View()->sPayment['name']))
    {
      return;
    }
    //get config by payment id
    $moptPayoneMain                 = $this->Application()->PayoneMain();
    $paymentId                      = $subject->View()->sPayment['id'];
    $config                         = $moptPayoneMain->getPayoneConfig($paymentId);
    $basketValue                    = $subject->View()->sAmount;
    $userData                       = $subject->View()->sUserData;
    $billingAddressData             = $userData['billingaddress'];
    $billingAddressData['country']  = $billingAddressData['countryID'];
    $shippingAddressData            = $userData['shippingaddress'];
    $shippingAddressData['country'] = $shippingAddressData['countryID'];
    $session                        = Shopware()->Session();
    $userId                         = $session->sUserId;

    //check if addresscheck is active for billingadress and check
    $billingAddressChecktype = $moptPayoneMain->getHelper()->isBillingAddressToBeCheckedWithBasketValue($config, $basketValue);

    if ($session->moptAddressCheckNeedsUserVerification)
    {
      $billingAddressChecktype     = false;
    }
    $userBillingAddressCheckData = $moptPayoneMain->getHelper()->getBillingAddresscheckDataFromUserId($userId);
    if ($billingAddressChecktype
            && !$moptPayoneMain->getHelper()->isBillingAddressCheckValid($config['adresscheckLifetime'], $userBillingAddressCheckData['moptPayoneAddresscheckResult'], $userBillingAddressCheckData['moptPayoneAddresscheckDate']))
    {
      //perform check
      $params   = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($billingAddressData, $billingAddressData, $paymentId);
      $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $billingAddressChecktype);

      //handle result
      $errors = $this->handleBillingAddressCheckResult($response, $config, $userId, $subject, $billingAddressData);
      if (!empty($errors['sErrorFlag']))
      {
        $ret['sErrorFlag']     = $errors['sErrorFlag'];
        $ret['sErrorMessages'] = $errors['sErrorMessages'];

        $arguments->setReturn($ret);
        return;
      }
    }

    //get shippingaddress attributes
    $shippingAttributes       = $moptPayoneMain->getHelper()->getShippingAddressAttributesFromUserId($userId);
    //check if addresscheck is active for shippingadress and data is valid and check if necessary
    $shippingAddressChecktype = $moptPayoneMain->getHelper()->isShippingAddressToBeCheckedWithBasketValue($config, $basketValue);

    if ($session->moptAddressCheckNeedsUserVerification)
    {
      $shippingAddressChecktype = false;
    }

    if ($shippingAddressChecktype
            && !$moptPayoneMain->getHelper()->isShippingAddressCheckValid($config['adresscheckLifetime'], $shippingAttributes['moptPayoneAddresscheckResult'], $shippingAttributes['moptPayoneAddresscheckDate']))
    {
      //perform check
      $params   = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($shippingAddressData, $shippingAddressData, $paymentId);
      $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $shippingAddressChecktype);

      //handle result
      $errors = $this->handleShippingAddressCheckResult($response, $config, $userId, $subject, $shippingAddressData);
      if (!empty($errors['sErrorFlag']))
      {
        $ret['sErrorFlag']     = $errors['sErrorFlag'];
        $ret['sErrorMessages'] = $errors['sErrorMessages'];

        $arguments->setReturn($ret);
        return;
      }
    }

    //check if consumerscore is active and check if necessary
    $cosumerScoreChecktype = $moptPayoneMain->getHelper()->isConsumerScoreToBeCheckedWithBasketValue($config, $basketValue);
    $userConsumerScoreData = $moptPayoneMain->getHelper()->getConsumerScoreDataFromUserId($userId);
    if ($cosumerScoreChecktype
            && !$moptPayoneMain->getHelper()->isCosumerScoreCheckValid($config['consumerscoreLifetime'], $userConsumerScoreData['moptPayoneConsumerscoreResult'], $userConsumerScoreData['moptPayoneConsumerscoreDate']))
    {

      //perform check if prechoice is configured
      if ($config['consumerscoreCheckMoment'] == 0)
      {
        $params   = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $paymentId);
        $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
        if (!$this->handleConsumerScoreCheckResult($response, $config, $userId))
        {
          //cancel, redirect to payment choice
          $subject->forward('payment', 'account', null, array('sTarget' => 'checkout'));
        }
      }
      else
      {
        //set sessionflag if after paymentchoice is configured
        $session->moptConsumerScoreCheckNeedsUserAgreement = true;
        $session->moptPaymentId = $subject->View()->sPayment['id'];
      }
    }
  }

  /**
   * 
   * billingaddress addresscheck
   * 
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function onValidateStep2(Enlight_Hook_HookArgs $arguments)
  {
    $ret = $arguments->getReturn();

    //@TODO do nothing when non pay1 method is choosen - tricky and maybe not possible

    if (!empty($ret['sErrorMessages']))
    {
      $arguments->setReturn($ret);
      return;
    }

    //get config data from main
    $moptPayoneMain = $this->Application()->PayoneMain();
    $config         = $moptPayoneMain->getPayoneConfig();
    $session        = Shopware()->Session();

    //get basket value
    $basket      = Shopware()->Modules()->Basket()->sGetBasket();
    $basketValue = $basket['AmountNumeric'];

    //perform check if addresscheck is enabled
    $billingAddressChecktype = $moptPayoneMain->getHelper()->isBillingAddressToBeChecked($config);

    if ($session->moptPayoneBillingAddresscheckResult)
    {
      $billingAddressChecktype = false;
    }

    if ($billingAddressChecktype)
    {
      //if nothing in basket, don't check and  just reset the validation date and result
      if (!$basketValue)
      {
        $userId = $session->sUserId;
        $moptPayoneMain->getHelper()->resetAddressCheckData($userId);
        return;
      }

      //no check when basket value outside configured values
      if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket'])
      {
        return;
      }
      else
      {
        $post             = $_POST["register"];
        $billingFormData  = $post['billing'];
        $personalFormData = $post['personal'];

        $params   = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($billingFormData, $personalFormData);
        $response = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $billingAddressChecktype);

        //@TODO refactor, extract methods
        if ($response->getStatus() == 'VALID')
        {
          $session   = Shopware()->Session();
          $secStatus = $response->getSecstatus();
          //check secstatus and config
          if ($secStatus == 10)
          {
            //valid address returned -> save result to session
            $session->moptPayoneBillingAddresscheckResult = serialize($response);
          }
          else
          {
            //secstatus must be 20 - corrected address returned
            switch ($config['adresscheckAutomaticCorrection'])
            {
              case 0: //auto correction
                {
                  Shopware()->Modules()->Admin()->sSYSTEM->_POST['street'] = $response->getStreetname();
                  Shopware()->Modules()->Admin()->sSYSTEM->_POST['streetnumber'] = $response->getStreetnumber();
                  Shopware()->Modules()->Admin()->sSYSTEM->_POST['zipcode'] = $response->getZip();
                  Shopware()->Modules()->Admin()->sSYSTEM->_POST['city'] = $response->getCity();
                }
                break;
              case 1: // no correction
                {
                  $session->moptPayoneBillingAddresscheckResult = serialize($response);
                }
                break;
              case 2: //depends on user
                {
                  // add addressdata to template 
                  $session->moptAddressCheckNeedsUserVerification = true;
                  $session->moptAddressCheckOriginalAddress = $billingFormData;
                  $session->moptAddressCheckCorrectedAddress = serialize($response);
                }
                break;
            }
          }

          $arguments->setReturn($ret);
          return;
        }
        if ($response->getStatus() == 'INVALID' || $response->getStatus() == 'ERROR')
        {
          $ret['sErrorFlag']['mopt_payone_configured_message']     = true;
          $ret['sErrorFlag']['mopt_payone_addresscheck']           = true;
          $ret['sErrorMessages']['mopt_payone_configured_message'] = $config['adresscheckFailureMessage'];
          $ret['sErrorMessages']['mopt_payone_addresscheck']       = utf8_encode($response->getCustomermessage());

          $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);
          $session->moptPayoneBillingAddresscheckResult = serialize($response);

          $request = $this->Application()->Front()->Request(); // used to forward user
          switch ($config['adresscheckFailureHandling'])
          {
            case 0: //cancel transaction -> redirect to payment choice
              {
                $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                $arguments->setReturn($ret);
                return;
              }
              break;
            case 1: // reenter address -> redirect to address form
              {
                $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                $arguments->setReturn($ret);
                return;
              }
              break;
            case 2: // perform consumerscore check
              {
                $params   = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingFormData);
                $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
                if (!$this->handleConsumerScoreCheckResult($response, $config, $userId))
                {
                  $this->forward($request, 'billing', 'account', null, array('sTarget' => 'checkout'));
                  $arguments->setReturn($ret);
                  return;
                }
              }
              break;
            case 3: // proceed
              {
                return;
              }
              break;
          }

          $arguments->setReturn($ret);
          return;
        }
      }
    }

    $arguments->setReturn($ret);
  }

  /**
   * save addresscheck result
   *
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function onUpdateBilling(Enlight_Hook_HookArgs $arguments)
  {
    $session = Shopware()->Session();

    if (!($result = unserialize($session->moptPayoneBillingAddresscheckResult)))
    {
      return;
    }

    $userId         = $session->sUserId;
    $moptPayoneMain = $this->Application()->PayoneMain();
    $config         = $moptPayoneMain->getPayoneConfig();

    if ($result->getStatus() == 'INVALID' || $result->getStatus() == 'ERROR')
    {
      $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $result);
    }
    else
    {
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($result->getPersonstatus(), $config);
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
      $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $result, $mappedPersonStatus);
    }

    unset($session->moptPayoneBillingAddresscheckResult);
  }

  /**
   * 
   * shipmentaddress addresscheck
   * 
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function onValidateStep2ShippingAddress(Enlight_Hook_HookArgs $arguments)
  {
    $returnValues = $arguments->getReturn();

    if (!empty($returnValues['sErrorMessages']))
    {
      return;
    }

    //get config data from main
    $moptPayoneMain = $this->Application()->PayoneMain();
    $config         = $moptPayoneMain->getPayoneConfig();

    //check if addresscheck is enabled
    if ($config['adresscheckActive'])
    {
      $shippingAddressChecktype = $moptPayoneMain->getHelper()->getAddressChecktypeFromId($config['adresscheckShippingAdress']);

      //@TODO check if basketvalue is within configured boundaries
      //return if shipping address checkmode is set to "no check"
      if (!$shippingAddressChecktype)
      {
        return;
      }

      $session          = Shopware()->Session();
      $userId           = $session->sUserId;
      $post             = $_POST["register"];
      $shippingFormData = $post['shipping'];
      $personalFormData = $post['personal'];
      $params           = $moptPayoneMain->getParamBuilder()->getAddressCheckParams($shippingFormData, $personalFormData);
      $response         = $this->performAddressCheck($config, $params, $this->Application()->PayoneBuilder(), $moptPayoneMain, $shippingAddressChecktype);

      //@TODO handle ERROR, VALID, INVALID and move to feedbackhandler
      if ($response->getStatus() == 'VALID')
      {
        $secStatus = $response->getSecstatus();
        //@TODO check secstatus and config
        if ($secStatus == 10)
        {
          //valid address returned, save result to session
          $session->moptPayoneShippingAddresscheckResult = serialize($response);
        }
        else
        {
          //@TODO secstatus must be 20 - corrected address returned
          switch ($config['adresscheckAutomaticCorrection'])
          {
            case 0: //auto correction
              {
                $session->moptPayoneShippingAddresscheckResult = serialize($response);
                Shopware()->Modules()->Admin()->sSYSTEM->_POST['register']['shipping']['street'] = $response->getStreetname();
                Shopware()->Modules()->Admin()->sSYSTEM->_POST['register']['shipping']['streetnumber'] = $response->getStreetnumber();
                Shopware()->Modules()->Admin()->sSYSTEM->_POST['register']['shipping']['zipcode'] = $response->getZip();
                Shopware()->Modules()->Admin()->sSYSTEM->_POST['register']['shipping']['city'] = $response->getCity();
              }
              break;
            case 1: // no correction
              {
                $session->moptPayoneShippingAddresscheckResult = serialize($response);
              }
              break;
            case 2: //depends on user
              {
                // add addressdata to template 
                $session->moptShippingAddressCheckNeedsUserVerification = true;
                $session->moptShippingAddressCheckOriginalAddress = $shippingFormData;
                $session->moptShippingAddressCheckCorrectedAddress = serialize($response);
              }
              break;
          }
        }

        //save corrected address or status to session in onUpdateShipping
        $arguments->setReturn($returnValues);
        return;
      }
      if ($response->getStatus() == 'INVALID' || $response->getStatus() == 'ERROR')
      {
        $returnValues['sErrorFlag']['mopt_payone_addresscheck']     = true;
        $returnValues['sErrorMessages']['mopt_payone_addresscheck'] = utf8_encode($response->getCustomermessage());


//        $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $response);
        $request = $this->Application()->Front()->Request(); // used to forward user
        $session->moptPayoneShippingAddresscheckResult = serialize($response);

        switch ($config['adresscheckFailureHandling'])
        {
          case 0: //cancel transaction -> redirect to payment choice
            {
              $arguments->setReturn($returnValues);
              $this->forward($request, 'index', 'register', null, array('sTarget' => 'checkout'));
              return;
            }
            break;
          case 1: // reenter address -> redirect to address form
            {
              $arguments->setReturn($returnValues);
              $this->forward($request, 'index', 'register', null, array('sTarget' => 'checkout'));
              return;
            }
            break;
          case 2: // perform consumerscore check
            {
              $params   = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($shippingAddressData);
              $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
              if (!$this->handleConsumerScoreCheckResult($response, $config, $userId))
              {
                //cancel transaction
                $arguments->setReturn($returnValues);
                $this->forward($request, 'index', 'register', null, array('sTarget' => 'checkout'));
                return;
              }
            }
            break;
          case 3: // proceed
            {
              return;
            }
            break;
        }


        $arguments->setReturn($returnValues);
        return;
      }
    }

    $arguments->setReturn($returnValues);
    return;
  }

  /**
   * save addresscheck result
   *
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function onUpdateShipping(Enlight_Hook_HookArgs $arguments)
  {
    $session = Shopware()->Session();

    if (!($result = unserialize($session->moptPayoneShippingAddresscheckResult)))
    {
      return;
    }

    $userId         = $session->sUserId;
    $moptPayoneMain = $this->Application()->PayoneMain();
    $config         = $moptPayoneMain->getPayoneConfig();

    if ($result->getStatus() == 'INVALID' || $result->getStatus() == 'ERROR')
    {
      $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $result);
    }
    else
    {
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($result->getPersonstatus(), $config);
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
      $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $result, $mappedPersonStatus);
    }
    unset($session->moptPayoneShippingAddresscheckResult);
  }

  /**
   * consumerscore check after choice if payment method
   * 
   * @param Enlight_Hook_HookArgs $arguments 
   */
  public function onValidateStep3(Enlight_Hook_HookArgs $arguments)
  {
    $returnValues = $arguments->getReturn();

    if (!empty($returnValues['sErrorMessages']))
    {
      $arguments->setReturn($returnValues); //@TODO check if neccessary
      return;
    }

    $userId         = Shopware()->Session()->sUserId;
    $post           = $_POST['moptPaymentData'];
    $paymentId      = $returnValues['paymentData']['name'];
    $moptPayoneMain = $this->Application()->PayoneMain();

    //check if pay1 method, exit if not and delete pament data 
    if (!preg_match('#mopt_payone__#', $paymentId))
    {
      $moptPayoneMain->getHelper()->deletePaymentData($userId);
      return;
    }

    if ($_POST['register']['payment'] === 'mopt_payone_creditcard')
    {
      $paymentId = $_POST['register']['payment'];
    }

    $paymentData = $moptPayoneMain->getFormHandler()->processPaymentForm($paymentId, $post);

    if (count($paymentData['sErrorFlag']))
    {
      $error = true;
    }

    if ($error)
    {
      $sErrorMessages[]                               = Shopware()->Snippets()->getNamespace('frontend/account/internalMessages')->get('ErrorFillIn', 'Please fill in all red fields');
      $returnValues['checkPayment']['sErrorFlag']     = $paymentData['sErrorFlag'];
      $returnValues['checkPayment']['sErrorMessages'] = $sErrorMessages;
    }
    else
    {
      $paymentId       = $_POST['register']['payment'];
      $config          = $moptPayoneMain->getPayoneConfig($paymentId);
      //get user data
      $user            = Shopware()->Modules()->Admin()->sGetUserData();
      $userData        = $user['additional']['user'];
      $billingFormData = $user['billingaddress'];

      if ($returnValues['paymentData']['name'] === 'mopt_payone__acc_debitnote')
      {
        //check if bankaccountcheck is enabled 
        $bankAccountChecktype = $moptPayoneMain->getHelper()->getBankAccountCheckType($config);
        if ($bankAccountChecktype === 0 || $bankAccountChecktype === 1)
        {
          //perform bankaccountcheck
          $params = $moptPayoneMain->getParamBuilder()->buildBankaccountcheck($paymentId, $bankAccountChecktype, $billingFormData['countryID'], $paymentData['formData']);

          $payoneServiceBuilder = $this->Application()->PayoneBuilder();
          $service              = $payoneServiceBuilder->buildServiceVerificationBankAccountCheck();
          $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                          'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
                  ));

          $request  = new Payone_Api_Request_BankAccountCheck($params);
          $response = $service->check($request);

          if ($response->getStatus() == 'ERROR' || $response->getStatus() == 'INVALID')
          {
            $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid']     = true;
            $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = utf8_encode($response->getCustomermessage());
            Shopware()->Session()->moptPayment = $post;
            $arguments->setReturn($returnValues);
            return;
          }

          if ($response->getStatus() == 'BLOCKED')
          {
            $returnValues['checkPayment']['sErrorFlag']['mopt_payone__account_invalid']     = true;
            $returnValues['checkPayment']['sErrorMessages']['mopt_payone__account_invalid'] = 'Zahlung mit der angegebenen Bankverbindung zur Zeit leider nicht möglich.';
            Shopware()->Session()->moptPayment = $post;
            $arguments->setReturn($returnValues);
            return;
          }
        }
      }

      if ($config['consumerscoreActive'] && $config['consumerscoreCheckMoment'] == 1)
      {
        //check if consumerscore is still valid or needs to be checked
        if (!$moptPayoneMain->getHelper()->isCosumerScoreCheckValid($config['consumerscoreLifetime'], $userData['moptPayoneConsumerscoreResult'], $userData['moptPayoneConsumerscoreDate']))
        {
          // add flag and data to session 
          $session->moptConsumerScoreCheckNeedsUserAgreement = true;
          $_SESSION['moptConsumerScoreCheckNeedsUserAgreement'] = true;
          $session->moptPaymentData = $paymentData;
          $_SESSION['moptPaymentData']                          = $paymentData;
          $session->moptPaymentId = $paymentId;
          $_SESSION['moptPaymentId']                            = $paymentId;
          //@TODO submit target
        }
      }

      //save data to table and session
      Shopware()->Session()->moptPayment = $post;
      $moptPayoneMain->getHelper()->savePaymentData($userId, $paymentData);
    }

    $arguments->setReturn($returnValues);
  }

  public function onUpdatePayment(Enlight_Hook_HookArgs $arguments)
  {
    $session = Shopware()->Session();

    if (!($result = unserialize($session->moptPayoneConsumerscorecheckResult)))
    {
      return;
    }

    $userId            = $session->sUserId;
    $mopt_payone__main = $this->Application()->PayoneMain();
    $mopt_payone__main->getHelper()->saveConsumerScoreCheckResult($userId, $result);

    unset($session->moptPayoneConsumerscorecheckResult);
  }

  protected function performAddressCheck($config, $params, $payoneServiceBuilder, $mopt_payone__main, $billingAddressChecktype)
  {
    $service = $payoneServiceBuilder->buildServiceVerificationAddressCheck();
    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                    'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
            ));

    $request = new Payone_Api_Request_AddressCheck($params);

    $request->setAddresschecktype($billingAddressChecktype);
    $request->setAid($config['subaccountId']);
    $request->setMode($mopt_payone__main->getHelper()->getApiModeFromId($config['adresscheckLiveMode']));

    $response = $service->check($request);
    return $response;
  }

  protected function performConsumerScoreCheck($config, $params, $payoneServiceBuilder)
  {
    $service = $payoneServiceBuilder->buildServiceVerificationConsumerscore();
    $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
                    'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
            ));

    $request = new Payone_Api_Request_Consumerscore($params);

    $billingAddressChecktype = 'NO';
    $request->setAddresschecktype($billingAddressChecktype);
    $request->setConsumerscoretype($config['consumerscoreCheckMode']);

    $response = $service->score($request);
    return $response;
  }

  protected function handleBillingAddressCheckResult($response, $config, $userId, $caller, $billingAddressData)
  {
    $ret = array();
    $moptPayoneMain = $this->Application()->PayoneMain();

    if ($response->getStatus() == 'VALID')
    {
      $secStatus          = $response->getSecstatus();
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($response->getPersonstatus(), $config);
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
      //check secstatus and config
      if ($secStatus == 10)
      {
        //valid address returned -> save result to db
        $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
      }
      else
      {
        //secstatus must be 20 - corrected address returned
        switch ($config['adresscheckAutomaticCorrection'])
        {
          case 0: //auto correction
            {
              //save result to db
              $moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $response);
              $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
            }
            break;
          case 1: // no correction
            {
              //save result to db
              $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
            }
            break;
          case 2: //depends on user
            {
              // add errormessage 
              $ret['sErrorFlag']['mopt_payone_configured_message']     = true;
//              $ret['sErrorFlag']['mopt_payone_corrected_message']      = true;
              $ret['sErrorMessages']['mopt_payone_configured_message'] = $config['adresscheckFailureMessage'];
//              $ret['sErrorMessages']['mopt_payone_corrected_message']  = 'Adresse konnte korrigiert werden ';
              $moptPayoneMain->getHelper()->saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus);
              $session->moptAddressCheckNeedsUserVerification = true;
              $session->moptAddressCheckOriginalAddress = $billingAddressData;
              $session->moptAddressCheckCorrectedAddress = serialize($response);
              $caller->forward('confirm', 'checkout', null, array('moptAddressCheckNeedsUserVerification' => true, 'moptAddressCheckOriginalAddress'       => $billingAddressData, 'moptAddressCheckCorrectedAddress'      => serialize($response), 'moptAddressCheckTarget'                => 'checkout'));
            }
            break;
        }
      }
    }
    else
    {
      $moptPayoneMain->getHelper()->saveBillingAddressError($userId, $response);

      switch ($config['adresscheckFailureHandling'])
      {
        case 0: //cancel transaction -> redirect to payment choice
          {
            $caller->forward('payment', 'account', null, array('sTarget' => 'checkout'));
          }
          break;
        case 1: // reenter address -> redirect to address form
          {
            $caller->forward('billing', 'account', null, array('sTarget' => 'checkout'));
          }
          break;
        case 2: // perform consumerscore check
          {
            //@TODO needs 2 be implemented
            $params   = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($billingAddressData, $config['paymentId']);
            $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
            $this->handleConsumerScoreCheckResult($response);
          }
          break;
      }
    }

    return $ret;
  }

  protected function handleShippingAddressCheckResult($response, $config, $userId, $subject, $shippingAddressData)
  {
    $ret = array();
    $moptPayoneMain = $this->Application()->PayoneMain();
    if ($response->getStatus() == 'VALID')
    {
      $secStatus          = $response->getSecstatus();
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringValue($response->getPersonstatus(), $config);
      $mappedPersonStatus = $moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
      //check secstatus and config
      if ($secStatus == 10)
      {
        //valid address returned -> save result to db
        $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);
      }
      else
      {
        //secstatus must be 20 - corrected address returned
        switch ($config['adresscheckAutomaticCorrection'])
        {
          case 0: //auto correction
            {
              //save result to db
              $moptPayoneMain->getHelper()->saveCorrectedShippingAddress($userId, $response);
              $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);
            }
            break;
          case 1: // no correction
            {
              //save result to db
              $moptPayoneMain->getHelper()->saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus);
            }
            break;
          case 2: //depends on user
            {
              // add errormessage 
              $ret['sErrorFlag']['mopt_payone_configured_message']     = true;
              $ret['sErrorFlag']['mopt_payone_corrected_message']      = true;
              $ret['sErrorMessages']['mopt_payone_configured_message'] = $config['adresscheckFailureMessage'];
              $ret['sErrorMessages']['mopt_payone_corrected_message']  = 'Adresse konnte korrigiert werden ';
              //add decisionbox to template 
              $session->moptShippingAddressCheckNeedsUserVerification = true;
              $session->moptShippingAddressCheckOriginalAddress = $shippingFormData;
              $session->moptShippingAddressCheckCorrectedAddress = serialize($response);
            }
            break;
        }
      }
    }
    else
    {
      $moptPayoneMain->getHelper()->saveShippingAddressError($userId, $response);

      switch ($config['adresscheckFailureHandling'])
      {
        case 0: //cancel transaction -> redirect to payment choice
          {
            $subject->forward('payment', 'account', null, array('sTarget' => 'checkout'));
          }
          break;
        case 1: // reenter address -> redirect to address form
          {
            $subject->forward('shipping', 'account', null, array('sTarget' => 'checkout'));
          }
          break;
        case 2: // perform consumerscore check
          {
            $params   = $moptPayoneMain->getParamBuilder()->getConsumerscoreCheckParams($shippingAddressData, $config['paymentId']);
            $response = $this->performConsumerScoreCheck($config, $params, $this->Application()->PayoneBuilder());
            if (!$this->handleConsumerScoreCheckResult($response, $config, $userId))
            {
              $subject->forward('payment', 'account', null, array('sTarget' => 'checkout'));
            }
          }
          break;
        case 3: //proceed
          {
            return;
          }
          break;
      }
    }

    return $ret;
  }

  protected function handleConsumerScoreCheckResult($response, $config, $userId)
  {
    $moptPayoneMain = $this->Application()->PayoneMain();

    // handle ERROR, VALID, INVALID 
    // @TODO move to feedbackhandler
    if ($response->getStatus() == 'VALID')
    {
      //save result
      $moptPayoneMain->getHelper()->saveConsumerScoreCheckResult($userId, $response);
      return true;
    }
    else
    {
      //save error
      $moptPayoneMain->getHelper()->saveConsumerScoreError($userId, $response);
      //choose next action according to config
      if ($config['consumerscoreFailureHandling'] == 0)
      {
        //cancel
        return false;
      }
      else
      {
        //proceed
        return true;
      }
    }

    return true;
  }

  /**
   * create payment methods
   */
  protected function createPayments()
  {
    $mopt_payone__paymentMethods = $this->helper->mopt_payone__getPaymentMethods();

    foreach ($mopt_payone__paymentMethods as $paymentMethod)
    {
      if ($this->Payments()->findOneBy(array('name' => $paymentMethod['name'])))
      {
        continue;
      }

      if ($paymentMethod['template'] == null)
      {
        $this->createPayment(array(
            'name'                  => $paymentMethod['name'],
            'description'           => $paymentMethod['description'],
//          'action'                => $paymentMethod['action'],
            'action'                => 'mopt_payment_payone',
            'active'                => 1,
            'position'              => $paymentMethod['position'],
            'additionalDescription' => 'Pay save and secured through our payment service.',
//          'additionalDescription' => $paymentMethod['additionalDescription'],
        ));
      }
      else
      {
        $this->createPayment(array(
            'name'                  => $paymentMethod['name'],
            'description'           => $paymentMethod['description'],
            'template'              => $paymentMethod['template'],
//          'action'                => $paymentMethod['action'],
            'action'                => 'mopt_payment_payone',
            'active'                => 1,
            'position'              => $paymentMethod['position'],
            'additionalDescription' => 'Pay save and secured through our payment service.',
//          'additionalDescription' => $paymentMethod['additionalDescription'],
        ));
      }
    }
  }

  /**
   * create tables, add coloumns
   */
  protected function createDatabase()
  {
    $em         = $this->Application()->Models();
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);

    $classes = array(
        $em->getClassMetadata('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog'),
    );

    try
    {
      $schemaTool->createSchema($classes);
    }
    catch (\Doctrine\ORM\Tools\ToolsException $e)
    {
      // ignore
    }
    $classes = array(
        $em->getClassMetadata('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'),
    );

    try
    {
      $schemaTool->createSchema($classes);
    }
    catch (\Doctrine\ORM\Tools\ToolsException $e)
    {
      // ignore
    }
    $classes = array(
        $em->getClassMetadata('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'),
    );

    try
    {
      $schemaTool->createSchema($classes);
    }
    catch (\Doctrine\ORM\Tools\ToolsException $e)
    {
      // ignore
    }

    // add payment table
    $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_payment_data` (`userId` int(11) NOT NULL,`moptPaymentData` text NOT NULL, PRIMARY KEY (`userId`))";
    Shopware()->Db()->exec($sql);
  }

  protected function addAttributes()
  {
    // user extension
    Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_result', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_date', 'date', true, null);
    Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
    Shopware()->Models()->addAttribute('s_user_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);
    // billing adress extension user
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_result', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_date', 'date', true, null);
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_result', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_date', 'date', true, null);
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
    Shopware()->Models()->addAttribute('s_user_billingaddress_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);
    // shipping adress extension
    Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_result', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_date', 'date', true, null);
    Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'addresscheck_personstatus', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_color', 'VARCHAR(1)', true, null);
    Shopware()->Models()->addAttribute('s_user_shippingaddress_attributes', 'mopt_payone', 'consumerscore_value', 'integer', true, null);
    // order extension
    Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'txid', 'integer', true, null);
    Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'status', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'sequencenumber', 'int(11)', true, null);
    Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'is_authorized', 'TINYINT(1)', true, null);
    Shopware()->Models()->addAttribute('s_order_attributes', 'mopt_payone', 'is_finally_captured', 'TINYINT(1)', true, null); //settlement for some payment-types
    // orderdetails(order articles) extension
    Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'payment_status', 'VARCHAR(100)', true, null);
    Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'shipment_date', 'date', true, null);
    Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'captured', 'double', true, null);
    Shopware()->Models()->addAttribute('s_order_details_attributes', 'mopt_payone', 'debit', 'double', true, null);

    Shopware()->Models()->generateAttributeModels(
            array(
                's_user_attributes',
                's_core_paymentmeans_attributes',
                's_user_billingaddress_attributes',
                's_user_shippingaddress_attributes',
                's_order_attributes',
                's_order_details_attributes',
            )
    );
  }

  /**
   * Creates and stores a payment item.
   */
  protected function createMenu()
  {
    $parent = $this->Menu()->findOneBy('label', 'Zahlungen');
    $item   = $this->createMenuItem(array(
        'label'  => 'PAYONE',
        'class'  => 'payoneicon',
        'active' => 1,
        'parent' => $parent,
            ));

    $parent = $item;

    $this->createMenuItem(array(
        'label'      => 'Konfiguration',
        'controller' => 'MoptConfigPayone',
        'action'     => 'Index',
        'class'      => 'sprite-wrench-screwdriver',
        'active'     => 1,
        'parent'     => $parent,
    ));
    $this->createMenuItem(array(
        'label'      => 'API-log',
        'controller' => 'MoptApilogPayone',
        'action'     => 'Index',
        'class'      => 'sprite-cards-stack',
        'active'     => 1,
        'parent'     => $parent,
    ));
    $this->createMenuItem(array(
        'label'      => 'Transaktionsstatus-Log',
        'controller' => 'MoptPayoneTransactionLog',
        'action'     => 'Index',
        'class'      => 'sprite-cards-stack',
        'active'     => 1,
        'parent'     => $parent,
    ));
    $this->createMenuItem(array(
        'label'      => 'Hilfe & Support',
        'controller' => 'MoptSupportPayone',
        'action'     => 'Index',
        'class'      => 'sprite-lifebuoy',
        'active'     => 1,
        'parent'     => $parent,
    ));
  }

  /**
   * @TODO move all addTemplateDir calls to controller init methods
   * Returns the path to a frontend controller for an event.
   *
   * @param Enlight_Event_EventArgs $args
   * @return string
   */
  public static function onGetControllerPathFrontendMoptPaymentPayone()
  {
    Shopware()->Template()->addTemplateDir(dirname(__FILE__) . '/Views/');
    return dirname(__FILE__) . '/Controllers/Frontend/MoptPaymentPayone.php';
  }

  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetControllerPathBackend()
  {
//    $this->Application()->Snippets()->addConfigDir(
//            $this->Path() . 'Snippets/'
//    );
    $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
    );
    return $this->Path() . 'Controllers/Backend/MoptPaymentPayone.php';
  }

  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetConfigControllerBackend()
  {
    $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
    );
    $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
    );
    return $this->Path() . 'Controllers/Backend/MoptConfigPayone.php';
  }

  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetApilogControllerBackend()
  {
    $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
    );
    $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
    );
    return $this->Path() . 'Controllers/Backend/MoptApilogPayone.php';
  }

  public function onGetTransactionLogControllerBackend()
  {
    $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
    );
    $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
    );
    return $this->Path() . 'Controllers/Backend/MoptPayoneTransactionLog.php';
  }

  /**
   * Returns the path to a backend controller for an event.
   *
   * @return string
   */
  public function onGetSupportControllerBackend()
  {
    $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
    );
    $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
    );
    return $this->Path() . 'Controllers/Backend/MoptSupportPayone.php';
  }

  function onPostDispatch(Enlight_Event_EventArgs $args)
  {
    $request  = $args->getSubject()->Request();
    $response = $args->getSubject()->Response();
    $view     = $args->getSubject()->View();

    if (!$request->isDispatched()
            || $response->isException()
            || $request->getModuleName() != 'frontend')
    {
      return;
    }

    $view->addTemplateDir($this->Path() . 'Views/');

    $session = Shopware()->Session();

    if ($request->getControllerName() == 'account' && $request->getActionName() == 'index')
    {
      if ($session->moptAddressCheckNeedsUserVerification)
      {
        $view->assign('moptAddressCheckNeedsUserVerification', $session->moptAddressCheckNeedsUserVerification);
        $view->extendsTemplate('frontend/account/mopt_billing.tpl');
      }
    }

    if ($request->getControllerName() == 'account' && $request->getActionName() == 'payment')
    {
      if ($_SESSION['moptConsumerScoreCheckNeedsUserAgreement'])
      {
        $view->assign('moptConsumerScoreCheckNeedsUserAgreement', $session->moptConsumerScoreCheckNeedsUserAgreement);
        $view->extendsTemplate('frontend/account/mopt_consumescore.tpl');
      }
    }

    if ($request->getControllerName() == 'checkout' && $request->getActionName() == 'confirm')
    {
      if ($session->moptAddressCheckNeedsUserVerification)
      {
        $view->assign('moptAddressCheckNeedsUserVerification', $session->moptAddressCheckNeedsUserVerification);
        $view->extendsTemplate('frontend/checkout/mopt_confirm.tpl');
      }
      $request = $args->getSubject()->Request();

      if ($request->getParam('moptAddressCheckNeedsUserVerification'))
      {
        $view->assign('moptAddressCheckNeedsUserVerification', $request->getParam('moptAddressCheckNeedsUserVerification'));
        $session->moptAddressCheckOriginalAddress = $request->getParam('moptAddressCheckOriginalAddress');
        $session->moptAddressCheckCorrectedAddress = $request->getParam('moptAddressCheckCorrectedAddress');
        $session->moptAddressCheckTarget = $request->getParam('moptAddressCheckTarget');
        $view->extendsTemplate('frontend/checkout/mopt_confirm.tpl');
      }

      if ($session->moptConsumerScoreCheckNeedsUserAgreement)
      {
        $view->assign('moptConsumerScoreCheckNeedsUserAgreement', $session->moptConsumerScoreCheckNeedsUserAgreement);
        $view->extendsTemplate('frontend/account/mopt_consumescore.tpl');
      }
    }
  }

  /**

   * Creates and returns the payone builder for an event.
   *
   * @param Enlight_Event_EventArgs $args
   * @return \Shopware_Components_Payone_Builder
   */
  public function onInitResourcePayoneBuilder(Enlight_Event_EventArgs $args)
  {
    $this->Application()->Loader()->registerNamespace(
            'Payone', $this->Path() . 'Components/Payone/'
    );

    $builder = new Payone_Builder();
    return $builder;
  }

  /**
   * Creates and returns the payone builder for an event.
   *
   * @param Enlight_Event_EventArgs $args
   * @return \Shopware_Components_Payone_Builder
   */
  public function onInitResourcePayoneMain(Enlight_Event_EventArgs $args)
  {
    $this->Application()->Loader()->registerNamespace('Mopt', $this->Path() . 'Components/Classes/');
    $moptPayoneMain = Mopt_PayoneMain::getInstance();
    return $moptPayoneMain;
  }

  public function onBackendRiskManagementPostDispatch(Enlight_Event_EventArgs $args)
  {
    $view = $args->getSubject()->View();

    // Add template directory
    $args->getSubject()->View()->addTemplateDir(
            $this->Path() . 'Views/'
    );

    $view->extendsTemplate('backend/mopt_risk_management/controller/main.js');
    $view->extendsTemplate('backend/mopt_risk_management/controller/risk_management.js');

    $view->extendsTemplate('backend/mopt_risk_management/store/risks.js');
    $view->extendsTemplate('backend/mopt_risk_management/store/trafficLights.js');

    $view->extendsTemplate('backend/mopt_risk_management/view/risk_management/container.js');
  }

  public function moptExtendController_Backend_Order(Enlight_Event_EventArgs $args)
  {
    $view = $args->getSubject()->View();
    $args->getSubject()->View()->addTemplateDir($this->Path() . 'Views/');
    $view->extendsTemplate('backend/mopt_payone_order/controller/detail.js');
    $view->extendsTemplate('backend/mopt_payone_order/model/position.js');
    $view->extendsTemplate('backend/mopt_payone_order/view/detail/overview.js');
    $view->extendsTemplate('backend/mopt_payone_order/view/detail/position.js');
  }

  /**
   * add attribute data to detail-data
   * @parent fnc head: protected function getList($filter, $sort, $offset, $limit)
   * 
   * @param Enlight_Event_EventArgs $args
   */
  public function Order__getList__after(Enlight_Event_EventArgs $args)
  {
    $return = $args->getReturn();
    $helper = $this->Application()->PayoneMain()->getHelper();

    if (empty($return['success']) || empty($return['data']))
    {
      return;
    }

    foreach ($return['data'] as &$order)
    {
      foreach ($order["details"] as &$orderDetail)
      {
        //get detail attribute
        $detailObj                         = Shopware()->Models()->getRepository('Shopware\Models\Order\Detail')->find($orderDetail['id']);
        $attribute                         = $helper->getOrCreateAttribute($detailObj);
        $orderDetail['moptPayoneCaptured'] = $attribute->getMoptPayoneCaptured();
        $orderDetail['moptPayoneDebit']    = $attribute->getMoptPayoneDebit();
      }
    }

    $args->setReturn($return);
  }

  public function sOrder__sSaveOrder__after(Enlight_Event_EventArgs $args)
  {
    $main        = $this->Application()->PayoneMain();
    $helper      = $main->getHelper();
    $orderNumber = $args->getReturn();

    if ($order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(array('number' => $orderNumber)))
    {
      $paymentId = $order->getPayment()->getId();
      $helper->mapTransactionStatus($order, $main->getPayoneConfig($paymentId), null, false);
      $helper->extractShippingCostAsOrderPosition($order);
    }
  }

  public function event_Shopware_Modules_Order_SaveOrderAttributes_FilterSQL(Enlight_Event_EventArgs $args)
  {
    $sql    = $args->getReturn();
    $sOrder = $args->getSubject();
    $db     = $sOrder->sSYSTEM->sDB_CONNECTION;
    $helper = $this->Application()->PayoneMain()->getHelper();

    //temporaryId
    $tempId    = $sOrder->sSYSTEM->sSESSION_ID;
    $tempOrder = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(array('temporaryId' => $tempId));

    if (!$tempOrder)
    {
      return;
    }

    $attributes = $helper->getOrCreateAttribute($tempOrder);

    //inject insert SQL
    $sql = str_replace("INSERT INTO s_order_attributes (", "INSERT INTO s_order_attributes (mopt_payone_txid, mopt_payone_status, mopt_payone_sequencenumber, " .
            "mopt_payone_is_authorized, mopt_payone_is_finally_captured, ", $sql);

    $sql = str_replace(" VALUES (", " VALUES (" .
            $db->quote($attributes->getMoptPayoneTxid()) . ', ' .
            $db->quote($attributes->getMoptPayoneStatus()) . ', ' .
            $db->quote($attributes->getMoptPayoneSequencenumber()) . ', ' .
            $db->quote($attributes->getMoptPayoneIsAuthorized()) . ', ' .
            $db->quote($attributes->getMoptPayoneIsFinallyCaptured()) . ', ', $sql);

    $args->setReturn($sql);
  }

  /**
   * Forward the request to the given controller, module and action with the given parameters.
   * copied from Enlight_Controller_Action
   * and customized
   *
   * @param object $request
   * @param string $action
   * @param string $controller
   * @param string $module
   * @param array  $params
   */
  public function forward($request, $action, $controller = null, $module = null, array $params = null)
  {
    if ($params !== null)
    {
      $request->setParams($params);
    }
    if ($controller !== null)
    {
      $request->setControllerName($controller);
      if ($module !== null)
      {
        $request->setModuleName($module);
      }
    }

    $request->setActionName($action)->setDispatched(false);
  }

}
