<?php

/**
 * $Id: $
 */
class Mopt_PayonePaymentHelper
{

  /**
   * adds Payone API value for creditcard
   * 
   * @param array $cardData
   * @return array
   */
  public function mapCardLetter($cardData)
  {
    foreach ($cardData as &$creditCard)
    {
      $start = strpos($creditCard['name'], 'mopt_payone__cc');

      if ($start === false)
      {
        continue;
      }

      $creditCardName = substr($creditCard['name'], $start, 19);

      switch ($creditCardName)
      {
        case "mopt_payone__cc_vis":
          $creditCard['short'] = 'V';
          break;
        case 'mopt_payone__cc_mas':
          $creditCard['short'] = 'M';
          break;
        case 'mopt_payone__cc_ame':
          $creditCard['short'] = 'A';
          break;
        case 'mopt_payone__cc_din':
          $creditCard['short'] = 'D';
          break;
        case 'mopt_payone__cc_jcb':
          $creditCard['short'] = 'J';
          break;
        case 'mopt_payone__cc_mae':
          $creditCard['short'] = 'O';
          break;
        case 'mopt_payone__cc_dis':
          $creditCard['short'] = 'C';
          break;
        case 'mopt_payone__cc_car':
          $creditCard['short'] = 'B';
          break;
      }
    }
    return $cardData;
  }

  /**
   * delete saved payment data
   *
   * @param string $userId 
   */
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

  /**
   * returns payment name
   *
   * @param string $paymentID
   * @return string 
   */
  public function getPaymentNameFromId($paymentID)
  {
    $sql         = 'SELECT `name` FROM `s_core_paymentmeans` WHERE id = ?';
    $paymentName = Shopware()->Db()->fetchOne($sql, $paymentID);

    return $paymentName;
  }

  /**
   * save payment data
   *
   * @param string $userId
   * @param array $paymentData 
   */
  public function savePaymentData($userId, $paymentData)
  {
    $sql         = 'replace into `s_plugin_mopt_payone_payment_data`' .
            '(`userId`,`moptPaymentData`) values (?,?)';
    $paymentData = serialize($paymentData['formData']);
    Shopware()->Db()->query($sql, array($userId, $paymentData));
  }

  /**
   * set configured default payment as payment method
   * 
   * @param string $userId 
   */
  public function setConfiguredDefaultPaymentAsPayment($userId)
  {
    $sql = "UPDATE s_user SET paymentID = ? WHERE id = ?";
    Shopware()->Db()->query($sql, array((int) Shopware()->Config()->Defaultpayment, (int) $userId));
  }

  /**
   * extract clearing data from response object
   *
   * @param object $response
   * @return boolean 
   */
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

  /**
   * returns clearing data
   *
   * @param string $orderId
   * @return array
   * @throws Exception 
   */
  public function getClearingDataFromOrderId($orderId)
  {
    $data = array();

    if (!$order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId))
    {
      throw new Exception("Order not found.");
    }

    $attribute    = $order->getAttribute();
    $clearingData = $attribute->getMoptPayoneClearingData();
    json_decode($clearingData, $data);

    return $data;
  }

  /**
   * check if given payment name is payone creditcard payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneCreditcard($paymentName)
  {
    if (preg_match('#mopt_payone__cc#', $paymentName) || $paymentName == 'mopt_payone_creditcard')
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone creditcard payment not grouped
   * it only checks for real existing payment methods not for virtual method "mopt_payone_creditcard"
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneCreditcardNotGrouped($paymentName)
  {
    if (preg_match('#mopt_payone__cc#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone sofortueberweisung payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneSofortuerberweisung($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_sofortueberweisung#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone giropay payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneGiropay($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_giropay#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone eps payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneEPS($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_eps#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone post eFinance payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayonePostEFinance($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_post_efinance#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone post finance card payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayonePostFinanceCard($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_post_finance_card#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone iDeal payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneIDeal($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_ideal#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone paypal payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayonePaypal($paymentName)
  {
    if (preg_match('#mopt_payone__ewallet_paypal#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone debitnote payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneDebitnote($paymentName)
  {
    if (preg_match('#mopt_payone__acc_debitnote#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone invoice payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneInvoice($paymentName)
  {
    if (preg_match('#mopt_payone__acc_invoice#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone pay in advance payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayonePayInAdvance($paymentName)
  {
    if (preg_match('#mopt_payone__acc_payinadvance#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone cash on delivery payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneCashOnDelivery($paymentName)
  {
    if (preg_match('#mopt_payone__acc_cashondel#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone billsafe payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneBillsafe($paymentName)
  {
    if (preg_match('#mopt_payone__fin_billsafe#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone payment method
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayonePaymentMethod($paymentName)
  {
    if (preg_match('#mopt_payone__#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone instant bank transfer payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneInstantBankTransfer($paymentName)
  {
    if (preg_match('#mopt_payone__ibt#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone finance payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneFinance($paymentName)
  {
    if (preg_match('#mopt_payone__fin#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * get online bank transfer type for api communication
   *
   * @param string $paymentName
   * @return string 
   */
  public function getOnlineBankTransferTypeFromPaymentName($paymentName)
  {
    if ($this->isPayoneSofortuerberweisung($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::INSTANT_MONEY_TRANSFER;
    }

    if ($this->isPayoneGiropay($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::GIROPAY;
    }

    if ($this->isPayoneEPS($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::EPS_ONLINE_BANK_TRANSFER;
    }

    if ($this->isPayonePostEFinance($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::POSTFINANCE_EFINANCE;
    }

    if ($this->isPayonePostFinanceCard($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::POSTFINANCE_CARD;
    }

    if ($this->isPayoneIDeal($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::IDEAL;
    }

    return '';
  }

}