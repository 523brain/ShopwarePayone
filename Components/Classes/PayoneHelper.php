<?php

/**
 * $Id: $
 */
class Mopt_PayoneHelper
{

  /**
   * returns array of PayOne paymetn methods
   * 
   * payment types are grouped
   * mopt_payone__[group]_[brand]
   *
   * @return array 
   */
  public function mopt_payone__getPaymentMethods()
  {
    return array(
        array(
            'name'        => 'mopt_payone__cc_visa',
            'description' => 'PAYONE Visa',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 1,),
        array(
            'name'        => 'mopt_payone__cc_mastercard',
            'description' => 'PAYONE Mastercard',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 2,),
        array(
            'name'        => 'mopt_payone__cc_american_express',
            'description' => 'PAYONE American Express',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 3,),
        array(
            'name'        => 'mopt_payone__cc_carte_blue',
            'description' => 'PAYONE Carte Blue',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 4,),
        array(
            'name'        => 'mopt_payone__cc_diners_club',
            'description' => 'PAYONE Diners Club',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 5,),
        array(
            'name'        => 'mopt_payone__cc_discover',
            'description' => 'PAYONE Discover',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 6,),
        array(
            'name'        => 'mopt_payone__cc_jcb',
            'description' => 'PAYONE JCB',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 7,),
        array(
            'name'        => 'mopt_payone__cc_maestro_international',
            'description' => 'PAYONE Maestro International',
            'template'    => 'mopt_paymentmean_creditcard.tpl',
            'position'    => 8,),
        array(
            'name'        => 'mopt_payone__ibt_sofortueberweisung',
            'description' => 'PAYONE Sofortüberweisung',
            'template'    => 'mopt_paymentmean_sofort.tpl',
            'position'    => 9,),
        array(
            'name'        => 'mopt_payone__ibt_giropay',
            'description' => 'PAYONE Giropay',
            'template'    => 'mopt_paymentmean_giropay.tpl',
            'position'    => 10,),
        array(
            'name'        => 'mopt_payone__ibt_eps',
            'description' => 'PAYONE eps',
            'template'    => 'mopt_paymentmean_eps.tpl',
            'position'    => 11,),
        array(
            'name'        => 'mopt_payone__ibt_post_efinance',
            'description' => 'PAYONE Post-Finance EFinance',
            'template'    => null,
            'position'    => 12,),
        array(
            'name'        => 'mopt_payone__ibt_post_finance_card',
            'description' => 'PAYONE Post-Finance Card',
            'template'    => null,
            'position'    => 13,),
        array(
            'name'        => 'mopt_payone__ibt_ideal',
            'description' => 'PAYONE iDeal',
            'template'    => 'mopt_paymentmean_ideal.tpl',
            'position'    => 14,),
        array(
            'name'        => 'mopt_payone__ewallet_paypal',
            'description' => 'PAYONE PayPal',
            'template'    => null,
            'position'    => 15,),
        array(
            'name'        => 'mopt_payone__acc_debitnote',
            'description' => 'PAYONE Lastschrift',
            'template'    => 'mopt_paymentmean_debit.tpl',
            'position'    => 16,),
        array(
            'name'        => 'mopt_payone__acc_invoice',
            'description' => 'PAYONE Offene Rechnung',
            'template'    => null,
            'position'    => 17,),
        array(
            'name'        => 'mopt_payone__acc_payinadvance',
            'description' => 'PAYONE Vorkasse',
            'template'    => null,
            'position'    => 18,),
        array(
            'name'        => 'mopt_payone__acc_cashondel',
            'description' => 'PAYONE Nachnahme',
            'template'    => null,
            'position'    => 19,),
        array(
            'name'        => 'mopt_payone__fin_billsafe',
            'description' => 'PAYONE BillSAFE',
            'template'    => null,
            'position'    => 20,),
        array(
            'name'        => 'mopt_payone__fin_commerzfin',
            'description' => 'PAYONE CommerzFinanz',
            'template'    => null,
            'position'    => 21,),
    );
    ;
  }

  /**
   * adds Payone API value for creditcard
   * 
   * @param type $cardData
   * @return type
   */
  public function mapCardLetter($cardData)
  {
    foreach ($cardData as &$creditCard)
    {
      switch ($creditCard['name'])
      {
        case "mopt_payone__cc_visa":
          $creditCard['short'] = 'V';
          break;
        case 'mopt_payone__cc_mastercard':
          $creditCard['short'] = 'M';
          break;
        case 'mopt_payone__cc_american_express':
          $creditCard['short'] = 'A';
          break;
        case 'mopt_payone__cc_diners_club':
          $creditCard['short'] = 'D';
          break;
        case 'mopt_payone__cc_jcb':
          $creditCard['short'] = 'J';
          break;
        case 'mopt_payone__cc_maestro_international':
          $creditCard['short'] = 'O';
          break;
        case 'mopt_payone__cc_discover':
          $creditCard['short'] = 'C';
          break;
        case 'mopt_payone__cc_carte_blue':
          $creditCard['short'] = 'B';
          break;
      }
    }
    return $cardData;
  }

  /**
   * returns Payone API value for selected addresschecktype
   * 
   * @param type $id
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
        $checkType = 'BA';
        break;
      case 2:
        $checkType = 'PE';
        break;
    }

    return $checkType;
  }

  /**
   * returns Payone API value for selected addresschecktype
   * 
   * @param type $id
   * @return string
   */
  public function getConsumerScoreChecktypeFromId($id)
  {
    switch ($id)
    {
      case 0:
        $checkType = 'IH';
        break;
      case 1:
        $checkType = 'IA';
        break;
      case 2:
        $checkType = 'IB';
        break;
    }

    return $checkType;
  }

  /**
   * checks if addresscheck needs to be checked according to configuration
   * returns addresschecktype
   * 
   * @param type $config
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
   * @param type $config
   * @param type $basketValue
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
   * @param type $config
   * @param type $basketValue
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
   * @param type $config
   * @param type $basketValue
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
   * @param type $id
   * @return string
   */
  public function getApiModeFromId($id)
  {
    if ($id == 1)
    {
      return 'live';
    }
    else
    {
      return 'test';
    }
  }

  public function getUserScoringValue($personStatus, $config)
  {
    switch ($personStatus)
    {
      case 'NONE':
        {
          return $config['mapPersonCheck'];
        }
      case 'PPB':
        {
          return $config['mapKnowPreLastname'];
        }
      case 'PHB':
        {
          return $config['mapKnowLastname'];
        }
      case 'PAB':
        {
          return $config['mapNotKnowPreLastname'];
        }
      case 'PKI':
        {
          return $config['mapMultiNameToAdress'];
        }
      case 'PNZ':
        {
          return $config['mapUndeliverable'];
        }
      case 'PPV':
        {
          return $config['mapPersonDead'];
        }
      case 'PPF':
        {
          return $config['mapWrongAdress'];
        }

        break;
    }
  }

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

  public function isBillingAddressCheckValid($adresscheckLifetime, $moptPayoneAddresscheckResult, $moptPayoneAddresscheckDate)
  {
//    if ($moptPayoneAddresscheckResult != 'VALID')
//    {
//      return false;
//    }
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

  public function isShippingAddressCheckValid($adresscheckLifetime, $moptPayoneAddresscheckResult, $moptPayoneAddresscheckDate)
  {
//    if ($moptPayoneAddresscheckResult != 'VALID')
//    {
//      return false;
//    }
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

  public function isCosumerScoreCheckValid($consumerScoreCheckLifetime, $moptPayoneConsumerScoreCheckResult, $moptPayoneConsumerScoreCheckDate)
  {
//    if ($moptPayoneConsumerScoreCheckResult != 'VALID')
//    {
//      return false;
//    }
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

  public function saveBillingAddressCheckResult($userId, $response, $mappedPersonStatus)
  {
    //get user address id
    $sql       = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
    $billingId = Shopware()->Db()->fetchOne($sql, $userId);

    //save result
    $sql = 'UPDATE `s_user_billingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_personstatus=?, mopt_payone_addresscheck_result=?, mopt_payone_consumerscore_color=? WHERE billingID = ?';
    Shopware()->Db()->query($sql, array(date('Y-m-d'), $response->getPersonstatus(), $response->getStatus(), $mappedPersonStatus, $billingId));
  }

  public function saveShippingAddressCheckResult($userId, $response, $mappedPersonStatus)
  {
    //get user shipping address id
    $sql        = 'SELECT `id` FROM `s_user_shippingaddress` WHERE userID =?';
    $shippingId = Shopware()->Db()->fetchOne($sql, $userId);

    $sql = 'UPDATE `s_user_shippingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_personstatus=?, mopt_payone_addresscheck_result=?, mopt_payone_consumerscore_color=? WHERE shippingID =?';

    Shopware()->Db()->query($sql, array(date('Y-m-d'), $response->getPersonstatus(), $response->getStatus(), $mappedPersonStatus, $shippingId));
  }

  public function saveBillingAddressError($userId, $response)
  {
    //get user address id
    $sql       = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
    $billingId = Shopware()->Db()->fetchOne($sql, $userId);

    //save result
    $sql = 'UPDATE `s_user_billingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_personstatus=?, mopt_payone_addresscheck_result=? WHERE billingID = ?';
    Shopware()->Db()->query($sql, array(date('Y-m-d'), 'NONE', $response->getStatus(), $billingId));
  }

  public function saveShippingAddressError($userId, $response)
  {
    //get user address id
    $sql        = 'SELECT `id` FROM `s_user_shippingaddress` WHERE userID = ?';
    $shippingId = Shopware()->Db()->fetchOne($sql, $userId);

    //save result
    $sql = 'UPDATE `s_user_shippingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_result=? WHERE shippingID = ?';
    Shopware()->Db()->query($sql, array(date('Y-m-d'), $response->getStatus(), $shippingId));
  }

  public function saveConsumerScoreCheckResult($userId, $response)
  {
    $sql = 'UPDATE `s_user_attributes`' .
            'SET mopt_payone_consumerscore_date=?, mopt_payone_consumerscore_result=?, mopt_payone_consumerscore_color=?, mopt_payone_consumerscore_value=? WHERE userID = ?';
    Shopware()->Db()->query($sql, array(date('Y-m-d'), $response->getStatus(), $response->getScore(), $response->getScorevalue(), $userId));
  }

  public function saveConsumerScoreError($userId, $response)
  {
    $sql = 'UPDATE `s_user_attributes`' .
            'SET mopt_payone_consumerscore_date=?, mopt_payone_consumerscore_result=? WHERE userID = ?';
    Shopware()->Db()->query($sql, array(date('Y-m-d'), $response->getStatus(), $userId));
  }

  public function saveConsumerScoreDenied($userId)
  {
    $sql = 'UPDATE `s_user_attributes`' .
            'SET mopt_payone_consumerscore_date=?, mopt_payone_consumerscore_result=? WHERE userID = ?';
    Shopware()->Db()->query($sql, array(date('Y-m-d'), 'DENIED', $userId));
  }

  public function saveCorrectedBillingAddress($userId, $response)
  {
    //get user address id
    $sql = 'UPDATE `s_user_billingaddress` SET street=?, streetnumber=?, zipcode=?, city=?  WHERE userID = ?';
    Shopware()->Db()->query(
            $sql, array(
        $response->getStreetname(),
        $response->getStreetnumber(),
        $response->getZip(),
        $response->getCity(),
        $userId));
  }

  public function saveCorrectedShippingAddress($userId, $response)
  {
    //get user address id
    $sql = 'UPDATE `s_user_shippingaddress` SET street=?, streetnumber=?, zipcode=?, city=?  WHERE userID = ?';
    Shopware()->Db()->query(
            $sql, array(
        $response->getStreetname(),
        $response->getStreetnumber(),
        $response->getZip(),
        $response->getCity(),
        $userId));
  }

  public function resetAddressCheckData($userId)
  {
    $sql       = 'SELECT `id` FROM `s_user_billingaddress` WHERE userID = ?';
    $billingId = Shopware()->Db()->fetchOne($sql, $userId);

    $sql = 'UPDATE `s_user_billingaddress_attributes`' .
            'SET mopt_payone_addresscheck_date=?, mopt_payone_addresscheck_result=? WHERE billingID = ?';
    Shopware()->Db()->query($sql, array('NULL', 'NULL', $billingId));
  }

  public function deletePaymentData($userId)
  {
    $sql    = 'SELECT userId FROM s_plugin_mopt_payone_payment_data WHERE userId = ' . $userId;
    $result = Shopware()->Db()->fetchOne($sql);

    if ($result)
    {
      $sql = 'DELETE FROM s_plugin_mopt_payone_payment_data WHERE userId = ' . $userId;
      Shopware()->Db()->exec($sql);
    }
  }

  public function setPayonePrepaymentAsPayment($userId)
  {
    $sql       = "SELECT id FROM s_core_paymentmeans WHERE name LIKE 'mopt_payone__acc_payinadvance'";
    $paymentId = Shopware()->Db()->fetchOne($sql);

    $sql = 'UPDATE s_user SET paymentID = ? WHERE id = ?';
    Shopware()->Db()->query($sql, array($paymentId, $userId));
  }

  public function savePaymentData($userId, $paymentData)
  {
    $sql         = 'replace into `s_plugin_mopt_payone_payment_data`' .
            '(`userId`,`moptPaymentData`) values (?,?)';
    $paymentData = serialize($paymentData['formData']);
    Shopware()->Db()->query($sql, array($userId, $paymentData));
  }

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

  public function getPaymentNameFromId($paymentID)
  {
    $sql         = 'SELECT `name` FROM `s_core_paymentmeans` WHERE id = ?';
    $paymentName = Shopware()->Db()->fetchOne($sql, $paymentID);

    return $paymentName;
  }

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

  public function extractShippingCostAsOrderPosition($order)
  {
    //leave if no shipment costs are set
    if($order->getInvoiceShipping() == 0)
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
            . $db->quote($order->getInvoiceShipping()) . ", "
            . "'1', "
            . $db->quote($dispatch->getName()) . ", "
            . "'0', "
            . "'0', "
            . "'0',"
            . " NULL, "
            . "'4', "
            . "'0', "
            . "'0', "
            . "'19',"
            . " '');";
    $db->exec($sql);


    //set invoiceshipping 2 zero
    $sql = "UPDATE s_order
                  SET invoice_shipping = 0
                  WHERE id = " . $db->quote($order->getId());
    $db->exec($sql);
  }

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

  public function getClearingDataFromOrderTxid($transactionId)
  {
    $data = array();

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');
    $builder->where($builder->expr()->orx($builder->expr()->like('log.responseDetails', $builder->expr()->literal(
                                    '%txid=' . $transactionId . '%'))));

    $result = $builder->getQuery()->getArrayResult();

    $dataResponse = explode('|', $result[0]['responseDetails']);
    foreach ($dataResponse as $value)
    {
      $tmp           = explode('=', $value);
      $data[$tmp[0]] = $tmp[1];
    }

    if ($data['status'] == 'APPROVED')
    {
      return $data;
    }
    else
    {
      return false;
    }
  }

  public function extractClearingDataFromResponse($response)
  {
    $responseData = $response->toArray();

    foreach ($responseData as $key => $value)
    {
      if (strpos($key, 'clearing_') === false)
      {
        unset($responseData[$key]);
      }
    }

    if (empty($responseData))
    {
      return false;
    }

    return $responseData;
  }

}