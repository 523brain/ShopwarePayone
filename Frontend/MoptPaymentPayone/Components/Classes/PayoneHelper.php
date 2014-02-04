<?php

/**
 * $Id: $
 */
class Mopt_PayoneHelper
{

  /**
   * returns Payone API value for selected addresschecktype
   * 
   * @param string $id
   * @return string
   */
  public function getAddressChecktypeFromId($id)
  {
    switch ($id)
    {
      case 0:
        $checkType = false;
        break;
      case 1:
        $checkType = Payone_Api_Enum_AddressCheckType::BASIC;
        break;
      case 2:
        $checkType = Payone_Api_Enum_AddressCheckType::PERSON;
        break;
    }

    return $checkType;
  }

  /**
   * returns Payone API value for selected addresschecktype
   * 
   * @param string $id
   * @return string
   */
  public function getConsumerScoreChecktypeFromId($id)
  {
    switch ($id)
    {
      case 0:
        $checkType = Payone_Api_Enum_ConsumerscoreType::INFOSCORE_HARD;
        break;
      case 1:
        $checkType = Payone_Api_Enum_ConsumerscoreType::INFOSCORE_ALL;
        break;
      case 2:
        $checkType = Payone_Api_Enum_ConsumerscoreType::INFOSCORE_ALL_BONI;
        break;
    }

    return $checkType;
  }

  /**
   * checks if addresscheck needs to be checked according to configuration
   * returns addresschecktype
   * 
   * @param array $config
   * @return boolean
   */
  public function isBillingAddressToBeChecked($config)
  {
    if (!$config['adresscheckActive'])
    {
      return false;
    }

    $session = Shopware()->Session();
    if ($session->moptAddressCheckNeedsUserVerification)
    {
      return false;
    }

    $billingAddressChecktype = $this->getAddressChecktypeFromId($config['adresscheckBillingAdress']);

    return $billingAddressChecktype;
  }

  /**
   * checks if addresscheck needs to be checked according to configuration
   * returns addresschecktype
   * 
   * @param array $config
   * @param string $basketValue
   * @return boolean
   */
  public function isBillingAddressToBeCheckedWithBasketValue($config, $basketValue)
  {
    if (!$config['adresscheckActive'])
    {
      return false;
    }

    //no check when basket value outside configured values
    if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket'])
    {
      return false;
    }

    $billingAddressChecktype = $this->getAddressChecktypeFromId($config['adresscheckBillingAdress']);

    return $billingAddressChecktype;
  }

  /**
   * checks if addresscheck needs to be checked according to configuration
   * returns addresschecktype
   * 
   * @param array $config
   * @param string $basketValue
   * @return boolean
   */
  public function isShippingAddressToBeCheckedWithBasketValue($config, $basketValue)
  {
    if (!$config['adresscheckActive'])
    {
      return false;
    }

    //check if seperate shipping address is saved
    $sql        = 'SELECT `id` FROM `s_user_shippingaddress` WHERE userID = ?';
    $shippingId = Shopware()->Db()->fetchOne($sql, $userId);
    if (!$shippingId)
    {
      return false;
    }

    //no check when basket value outside configured values
    if ($basketValue < $config['adresscheckMinBasket'] || $basketValue > $config['adresscheckMaxBasket'])
    {
      return false;
    }

    $shippingAddressChecktype = $this->getAddressChecktypeFromId($config['adresscheckShippingAdress']);

    return $shippingAddressChecktype;
  }

  /**
   * checks if addresscheck needs to be checked according to configuration
   * returns addresschecktype
   * 
   * @param array $config
   * @param string $basketValue
   * @return boolean
   */
  public function isConsumerScoreToBeCheckedWithBasketValue($config, $basketValue)
  {
    if (!$config['consumerscoreActive'])
    {
      return false;
    }

    //no check when basket value outside configured values
    if ($basketValue < $config['consumerscoreMinBasket'] || $basketValue > $config['consumerscoreMaxBasket'])
    {
      return false;
    }

    $shippingAddressChecktype = $this->getConsumerScoreChecktypeFromId($config['consumerscoreCheckMode']);

    return $shippingAddressChecktype;
  }

  /**
   * returns Payone API value for sandbox/live mode
   * 
   * @param string $id
   * @return string
   */
  public function getApiModeFromId($id)
  {
    if ($id == 1)
    {
      return Payone_Enum_Mode::LIVE;
    }
    else
    {
      return Payone_Enum_Mode::TEST;
    }
  }

  /**
   * get user scoring value
   *
   * @param string $personStatus
   * @param array $config
   * @return string 
   */
  public function getUserScoringValue($personStatus, $config)
  {
    switch ($personStatus)
    {
      case Payone_Api_Enum_AddressCheckPersonstatus::NONE:
        {
          return $config['mapPersonCheck'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PPB:
        {
          return $config['mapKnowPreLastname'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PHB:
        {
          return $config['mapKnowLastname'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PAB:
        {
          return $config['mapNotKnowPreLastname'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PKI:
        {
          return $config['mapMultiNameToAdress'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PNZ:
        {
          return $config['mapUndeliverable'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PPV:
        {
          return $config['mapPersonDead'];
        }
      case Payone_Api_Enum_AddressCheckPersonstatus::PPF:
        {
          return $config['mapWrongAdress'];
        }

        break;
    }
  }

  /**
   * get user scoring color
   *
   * @param int $value
   * @return string 
   */
  public function getUserScoringColorFromValue($value)
  {
    switch ($value)
    {
      case 0:
        return 'R';
        break;
      case 1:
        return 'Y';
        break;
      case 2:
        return 'G';
        break;

      default:
        break;
    }
  }

  /**
   * get user scoring value
   *
   * @param string $color
   * @return int 
   */
  public function getUserScoringValueFromColor($color)
  {
    switch ($color)
    {
      case 'R':
        return 0;
        break;
      case 'Y':
        return 1;
        break;
      case 'G':
        return 2;
        break;
    }

    return $color;
  }

  /**
   * check if check is still valid
   *
   * @param int $adresscheckLifetime
   * @param string $moptPayoneAddresscheckResult
   * @param date $moptPayoneAddresscheckDate
   * @return boolean 
   */
  public function isBillingAddressCheckValid($adresscheckLifetime, $moptPayoneAddresscheckResult, $moptPayoneAddresscheckDate)
  {
    if (!$moptPayoneAddresscheckDate)
    {
      return false;
    }
    if ($moptPayoneAddresscheckDate->getTimestamp() < strtotime('-' . $adresscheckLifetime . ' days'))
    {
      return false;
    }

    return true;
  }

  /**
   * check if check is still valid
   *
   * @param string $adresscheckLifetime
   * @param string $moptPayoneAddresscheckResult
   * @param date $moptPayoneAddresscheckDate
   * @return boolean 
   */
  public function isShippingAddressCheckValid($adresscheckLifetime, $moptPayoneAddresscheckResult, $moptPayoneAddresscheckDate)
  {
    if (!$moptPayoneAddresscheckDate)
    {
      return false;
    }

    if ($moptPayoneAddresscheckDate->getTimestamp() < strtotime('-' . $adresscheckLifetime . ' days'))
    {
      return false;
    }

    return true;
  }

  /**
   * check if check is still valid
   *
   * @param string $consumerScoreCheckLifetime
   * @param string $moptPayoneConsumerScoreCheckResult
   * @param date $moptPayoneConsumerScoreCheckDate
   * @return boolean 
   */
  public function isCosumerScoreCheckValid($consumerScoreCheckLifetime, $moptPayoneConsumerScoreCheckResult, $moptPayoneConsumerScoreCheckDate)
  {
    if (!$moptPayoneConsumerScoreCheckDate)
    {
      return false;
    }

    if ($moptPayoneConsumerScoreCheckDate->getTimestamp() < strtotime('-' . $consumerScoreCheckLifetime . ' days'))
    {
      return false;
    }

    return true;
  }

  /**
   * save check result
   *
   * @param string $userId
   * @param object $response
   * @param string $mappedPersonStatus
   * @return mixed 
   */
  public function saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus)
  {
    if (!$userId)
    {
      return;
    }

    $user             = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $billing          = $user->getBilling();
    $billingAttribute = $this->getOrCreateBillingAttribute($billing);

    $billingAttribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
    $billingAttribute->setMoptPayoneAddresscheckPersonstatus($response->getPersonstatus());
    $billingAttribute->setMoptPayoneAddresscheckResult($response->getStatus());
    $billingAttribute->setMoptPayoneConsumerscoreColor($mappedPersonStatus);

    Shopware()->Models()->persist($billingAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save check result
   *
   * @param string $userId
   * @param object $response
   * @param string $mappedPersonStatus
   * @return mixed 
   */
  public function saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus)
  {
    if (!$userId)
    {
      return;
    }


    $user              = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $shiping           = $user->getShipping();
    $shippingAttribute = $this->getOrCreateShippingAttribute($shiping);

    $shippingAttribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
    $shippingAttribute->setMoptPayoneAddresscheckPersonstatus($response->getPersonstatus());
    $shippingAttribute->setMoptPayoneAddresscheckResult($response->getStatus());
    $shippingAttribute->setMoptPayoneConsumerscoreColor($mappedPersonStatus);

    Shopware()->Models()->persist($shippingAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save check error
   *
   * @param string $userId
   * @param object $response
   * @return mixed 
   */
  public function saveBillingAddressError($userId, $response)
  {
    if (!$userId)
    {
      return;
    }


    $user             = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $billing          = $user->getBilling();
    $billingAttribute = $this->getOrCreateBillingAttribute($billing);

    $billingAttribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
    $billingAttribute->setMoptPayoneAddresscheckPersonstatus('NONE');
    $billingAttribute->setMoptPayoneAddresscheckResult($response->getStatus());

    Shopware()->Models()->persist($billingAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save check error
   *
   * @param string $userId
   * @param object $response
   * @return mixed 
   */
  public function saveShippingAddressError($userId, $response)
  {
    if (!$userId)
    {
      return;
    }

    $user              = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $shiping           = $user->getShipping();
    $shippingAttribute = $this->getOrCreateShippingAttribute($shiping);

    $shippingAttribute->setMoptPayoneAddresscheckDate(date('Y-m-d'));
    $shippingAttribute->setMoptPayoneAddresscheckResult($response->getStatus());

    Shopware()->Models()->persist($shippingAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save check resuklt
   *
   * @param string $userId
   * @param object $response
   * @return mixed 
   */
  public function saveConsumerScoreCheckResult($userId, $response)
  {
    if (!$userId)
    {
      return;
    }

    $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $userAttribute = $this->getOrCreateUserAttribute($user);

    $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
    $userAttribute->setMoptPayoneConsumerscoreResult($response->getStatus());
    $userAttribute->setMoptPayoneConsumerscoreColor($response->getScore());
    $userAttribute->setMoptPayoneConsumerscoreValue($response->getScorevalue());

    Shopware()->Models()->persist($userAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save check error
   *
   * @param string $userId
   * @param object $response
   * @return mixed 
   */
  public function saveConsumerScoreError($userId, $response)
  {
    if (!$userId)
    {
      return;
    }

    $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $userAttribute = $this->getOrCreateUserAttribute($user);

    $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
    $userAttribute->setMoptPayoneConsumerscoreResult($response->getStatus());

    Shopware()->Models()->persist($userAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save check denied
   *
   * @param string $userId
   * @return mixed 
   */
  public function saveConsumerScoreDenied($userId)
  {
    if (!$userId)
    {
      return;
    }

    $user          = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $userAttribute = $this->getOrCreateUserAttribute($user);

    $userAttribute->setMoptPayoneConsumerscoreDate(date('Y-m-d'));
    $userAttribute->setMoptPayoneConsumerscoreResult('DENIED');

    Shopware()->Models()->persist($userAttribute);
    Shopware()->Models()->flush();
  }

  /**
   * save corrected billing address
   *
   * @param string $userId
   * @param object $response 
   */
  public function saveCorrectedBillingAddress($userId, $response)
  {
    $sql = 'UPDATE `s_user_billingaddress` SET street=?, streetnumber=?, zipcode=?, city=?  WHERE userID = ?';
    Shopware()->Db()->query(
            $sql, array(
        $response->getStreetname(),
        $response->getStreetnumber(),
        $response->getZip(),
        $response->getCity(),
        $userId));
  }

  /**
   * save corrected shipping address
   *
   * @param string $userId
   * @param object $response 
   */
  public function saveCorrectedShippingAddress($userId, $response)
  {
    $sql = 'UPDATE `s_user_shippingaddress` SET street=?, streetnumber=?, zipcode=?, city=?  WHERE userID = ?';
    Shopware()->Db()->query(
            $sql, array(
        $response->getStreetname(),
        $response->getStreetnumber(),
        $response->getZip(),
        $response->getCity(),
        $userId));
  }

  /**
   * reset address check data
   *
   * @param string $userId 
   */
  public function resetAddressCheckData($userId)
  {
    $sql       = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
    $billingId = Shopware()->Db()->fetchOne($sql, $userId);

    $sql = 'UPDATE `s_user_billingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_result=? WHERE billingID = ?';
    Shopware()->Db()->query($sql, array('NULL', 'NULL', $billingId));
  }

  /**
   * get address attributes
   *
   * @param string $userId
   * @return array 
   */
  public function getShippingAddressAttributesFromUserId($userId)
  {
    //get shippingaddress attribute
    $shippingAttributes = array();
    $shippingAttributes['moptPayoneAddresscheckResult'] = null;
    $shippingAttributes['moptPayoneAddresscheckDate']   = null;

    $sql        = 'SELECT `id` FROM `s_user_shippingaddress` WHERE userID = ?';
    $shippingId = Shopware()->Db()->fetchOne($sql, $userId);

    $sql    = 'SELECT `mopt_payone_addresscheck_result`, `mopt_payone_addresscheck_date` FROM `s_user_shippingaddress_attributes` WHERE shippingID = ?';
    $result = Shopware()->Db()->fetchAll($sql, $shippingId);

    if ($result)
    {
      $shippingAttributes['moptPayoneAddresscheckResult'] = $result[0]['mopt_payone_addresscheck_result'];
      $shippingAttributes['moptPayoneAddresscheckDate']   = DateTime::createFromFormat('Y-m-d', $result[0]['mopt_payone_addresscheck_date']);
    }

    return $shippingAttributes;
  }

  /**
   * get bank account check type
   *
   * @param array $config
   * @return int 
   */
  public function getBankAccountCheckType($config)
  {
    switch ($config['checkAccount'])
    {
      case null:
        $checkType = false;
        break;
      case 0:
        $checkType = false;
        break;
      case 1:
        $checkType = 0;
        break;
      case 2:
        $checkType = 1;
        break;
    }

    return $checkType;
  }

  /**
   * get consumer score
   *
   * @param array $user
   * @param array $config
   * @return int 
   */
  public function getScoreFromUserAccordingToPaymentConfig($user, $config)
  {
    //if addresscheck enabled
    if ($config['adresscheckActive'])
    {
      if ($config['adresscheckBillingAdress'] != 0)
      {
        //get addresscheckscore
        if (!$billingColor = $user['billingaddress']['moptPayoneConsumerscoreColor'])
        {
          $billingColor = -2;
        }
      }
      else
      {
        $billingColor = -1;
      }
      //if shipmentadresscheck enabled
      if ($config['adresscheckShippingAdress'] != 0)
      {
        //get shipmentadresscheck score
        if (!$shipmentColor = $user['shippingaddress']['moptPayoneConsumerscoreColor'])
        {
          $shipmentColor = -2;
        }
      }
      else
      {
        $shipmentColor = -1;
      }
    }
    else
    {
      $billingColor  = -1;
      $shipmentColor = -1;
    }

    //if consumerscore enabled
    if ($config['consumerscoreActive'])
    {
      //get consumerscore
      if (!$consumerScoreColor = $user['additional']['user']['moptPayoneConsumerscoreColor'])
      {
        $consumerScoreColor = -2;
      }
    }
    else
    {
      $consumerScoreColor = -1;
    }

    //map value G-1, Y-2, R-3
    $billingColor       = $this->getUserScoringValueFromColor($billingColor);
    $shipmentColor      = $this->getUserScoringValueFromColor($shipmentColor);
    $consumerScoreColor = $this->getUserScoringValueFromColor($consumerScoreColor);
    $lowestScore        = min($billingColor, $shipmentColor, $consumerScoreColor);

    //as default return new customer default value
    if ($lowestScore == -2)
    {
      $lowestScore = $config['consumerscoreDefault'];
    }

    switch ($lowestScore)
    {
      case 0:
        return 3;
        break;
      case 1:
        return 2;
        break;
      case 2:
        return 1;
        break;
    }

    //no check are active for this payment method
    return -3;
  }

  /**
   * get or create attribute data for given object
   *
   * @param object $object
   * @return \Shopware\Models\Attribute\OrderDetail
   * @throws Exception 
   */
  public function getOrCreateAttribute($object)
  {
    if ($attribute = $object->getAttribute())
    {
      return $attribute;
    }

    if ($object instanceof Shopware\Models\Order\Order)
    {
      if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Order')
              ->findOneBy(array('orderId' => $object->getId())))
      {
        $attribute = new Shopware\Models\Attribute\Order();
      }
    }
    elseif ($object instanceof Shopware\Models\Order\Detail)
    {
      if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\OrderDetail')
              ->findOneBy(array('orderDetailId' => $object->getId())))
      {
        $attribute = new Shopware\Models\Attribute\OrderDetail();
      }
    }
    else
    {
      throw new Exception('Unknown attribute base class');
    }

    $object->setAttribute($attribute);
    return $attribute;
  }

  /**
   * get or create attribute data for given object
   *
   * @param Billing $object
   * @return \Shopware\Models\Attribute\CustomerBilling
   * @throws Exception 
   */
  public function getOrCreateBillingAttribute($object)
  {
    if ($attribute = $object->getAttribute())
    {
      return $attribute;
    }

    if ($object instanceof Shopware\Models\Customer\Billing)
    {
      if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\CustomerBilling')
              ->findOneBy(array('customerBillingId' => $object->getId())))
      {
        $attribute = new Shopware\Models\Attribute\CustomerBilling();
      }
    }
    else
    {
      throw new Exception('Unknown attribute base class');
    }

    $object->setAttribute($attribute);
    return $attribute;
  }

  /**
   * get or create attribute data for given object
   *
   * @param Shipping $object
   * @return \Shopware\Models\Attribute\CustomerShipping
   * @throws Exception 
   */
  public function getOrCreateShippingAttribute($object)
  {
    if ($attribute = $object->getAttribute())
    {
      return $attribute;
    }

    if ($object instanceof Shopware\Models\Customer\Shipping)
    {
      if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\CustomerShipping')
              ->findOneBy(array('customerShippingId' => $object->getId())))
      {
        $attribute = new Shopware\Models\Attribute\CustomerShipping();
      }
    }
    else
    {
      throw new Exception('Unknown attribute base class');
    }

    $object->setAttribute($attribute);
    return $attribute;
  }

  /**
   * get or create attribute data for given object
   *
   * @param Customer $object
   * @return \Shopware\Models\Attribute\Customer
   * @throws Exception 
   */
  public function getOrCreateUserAttribute($object)
  {
    if ($attribute = $object->getAttribute())
    {
      return $attribute;
    }

    if ($object instanceof Shopware\Models\Customer\Customer)
    {
      if (!$attribute = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Customer')
              ->findOneBy(array('customerId' => $object->getId())))
      {
        $attribute = new Shopware\Models\Attribute\Customer();
      }
    }
    else
    {
      throw new Exception('Unknown attribute base class');
    }

    $object->setAttribute($attribute);
    return $attribute;
  }

  /**
   * map transaction status
   *
   * @param object $order
   * @param array $payoneConfig
   * @param string $payoneStatus
   * @param bool $useOrm 
   */
  public function mapTransactionStatus($order, $payoneConfig, $payoneStatus = null, $useOrm = true)
  {
    if ($payoneStatus === null)
    {
      $attributeData = $this->getOrCreateAttribute($order);
      $payoneStatus  = $attributeData->getMoptPayoneStatus();
    }

    //map payone status to shopware payment-status
    $configKey = 'state' . ucfirst($payoneStatus);
    if (isset($payoneConfig[$configKey]))
    {
      if ($shopwareState = Shopware()->Models()->getRepository('Shopware\Models\Order\Status')
              ->find($payoneConfig[$configKey]))
      {
        if ($useOrm)
        {
          $order->setPaymentStatus($shopwareState);
          Shopware()->Models()->persist($order);
          Shopware()->Models()->flush();
        }
        else
        {
          $db  = Shopware()->Db();
          $sql = "UPDATE s_order
                  SET cleared = " . $db->quote($shopwareState->getId()) . "
                  WHERE id = " . $db->quote($order->getId());
          $db->exec($sql);
        }
      }
    }
  }

  /**
   * extract shipping cost and insert as order position
   *
   * @param object $order
   * @return mixed 
   */
  public function extractShippingCostAsOrderPosition($order)
  {
    //leave if no shipment costs are set
    if ($order->getInvoiceShipping() == 0)
    {
      return;
    }

    $dispatch = $order->getDispatch();
    if (strpos($order->getPayment()->getName(), 'mopt_payone__') !== 0)
    {
      return false;
    }
    //insert shipping as new order detail
    $db  = Shopware()->Db();
    $sql = "INSERT INTO `s_order_details` (`id`, "
            . " `orderID`, "
            . "`ordernumber`, "
            . "`articleID`, "
            . "`articleordernumber`, "
            . "`price`, "
            . "`quantity`, "
            . "`name`, "
            . "`status`, "
            . "`shipped`, "
            . "`shippedgroup`, "
            . "`releasedate`, "
            . "`modus`, "
            . "`esdarticle`, "
            . "`taxID`, "
            . "`tax_rate`, "
            . "`config`) "
            . " VALUES ("
            . "NULL, "
            . $db->quote($order->getId()) . ", "
            . $db->quote($order->getNumber()) . ", "
            . "'0', "
            . "'SHIPPING', "
            . ($order->getTaxFree() ? $db->quote($order->getInvoiceShippingNet()) : $db->quote($order->getInvoiceShipping())) . ", "
            . "'1', "
            . $db->quote($dispatch->getName()) . ", "
            . "'0', "
            . "'0', "
            . "'0',"
            . " NULL, "
            . "'4', "
            . "'0', "
            . "'0', "
            . $db->quote(round(($order->getInvoiceShipping() / $order->getInvoiceShippingNet() - 1) * 100)) . ","
            . " '');";
    $db->exec($sql);


    // Set shipping details to zero since these informations are stored within the basket
    // of the corresponding order.
    $sql = "UPDATE s_order
                  SET invoice_shipping = 0,
                    invoice_shipping_net = 0
                  WHERE id = " . $db->quote($order->getId());
    $db->exec($sql);
  }

  /**
   * get consumerscore check data
   *
   * @param string $userId
   * @return array 
   */
  public function getConsumerScoreDataFromUserId($userId)
  {
    $userCconsumerScoreData = array();
    $userCconsumerScoreData['moptPayoneConsumerscoreResult'] = null;
    $userCconsumerScoreData['moptPayoneConsumerscoreDate']   = null;

    $sql    = 'SELECT `mopt_payone_consumerscore_result`, `mopt_payone_consumerscore_date` FROM `s_user_attributes` WHERE userID = ?';
    $result = Shopware()->Db()->fetchAll($sql, $userId);

    if ($result)
    {
      $userCconsumerScoreData['moptPayoneConsumerscoreResult'] = $result[0]['mopt_payone_consumerscore_result'];
      $userCconsumerScoreData['moptPayoneConsumerscoreDate']   = DateTime::createFromFormat('Y-m-d', $result[0]['mopt_payone_consumerscore_date']);
    }

    return $userCconsumerScoreData;
  }

  /**
   * get address check data
   *
   * @param string $userId
   * @return array 
   */
  public function getBillingAddresscheckDataFromUserId($userId)
  {
    $userBillingAddressCheckData = array();
    $userBillingAddressCheckData['moptPayoneAddresscheckResult'] = null;
    $userBillingAddressCheckData['moptPayoneAddresscheckDate']   = null;

    $sql       = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
    $billingId = Shopware()->Db()->fetchOne($sql, $userId);

    $sql    = 'SELECT `mopt_payone_addresscheck_result`, `mopt_payone_addresscheck_date` FROM `s_user_billingaddress_attributes` WHERE billingID = ?';
    $result = Shopware()->Db()->fetchAll($sql, $billingId);

    if ($result)
    {
      $userBillingAddressCheckData['moptPayoneAddresscheckResult'] = $result[0]['mopt_payone_addresscheck_result'];
      $userBillingAddressCheckData['moptPayoneAddresscheckDate']   = DateTime::createFromFormat('Y-m-d', $result[0]['mopt_payone_addresscheck_date']);
    }

    return $userBillingAddressCheckData;
  }

}