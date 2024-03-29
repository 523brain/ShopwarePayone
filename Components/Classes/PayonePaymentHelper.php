<?php

/**
 * $Id: $
 */
class Mopt_PayonePaymentHelper
{
  //Klarna CDN links for consents and legal terms
  const MOPT_PAYONE_KLARNA_CONSENT_DE = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_de/consent";
  const MOPT_PAYONE_KLARNA_CONSENT_AT = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_at/consent";
  
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_DE = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_de/invoice?fee=0";
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_AT = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_at/invoice?fee=0";
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/da_dk/invoice?fee=0";
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_FI = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/fi_fi/invoice?fee=0";
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_NL = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/nl_nl/invoice?fee=0";
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_NO = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/nb_no/invoice?fee=0";
  const MOPT_PAYONE_KLARNA_INVOICE_TERMS_SE = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/sv_se/invoice?fee=0";
    
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
   * @return boolean/array 
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
   * check if given payment name is a payone ewallet payment method
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isEWallet($paymentName)
  {
    if (preg_match('#mopt_payone__ewallet#', $paymentName))
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
   * check if given payment name is payone klarna payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneKlarna($paymentName)
  {
    if (preg_match('#mopt_payone__fin_klarna#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone klarna installment payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneKlarnaInstallment($paymentName)
  {
    if (preg_match('#mopt_payone__fin_klarna_installment#', $paymentName))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * check if given payment name is payone P24 payment
   *
   * @param string $paymentName
   * @return boolean 
   */
  public function isPayoneP24($paymentName)
  {
    if (preg_match('#mopt_payone__ibt_p24#', $paymentName))
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

    if ($this->isPayoneP24($paymentName))
    {
      return Payone_Api_Enum_OnlinebanktransferType::P24;
    }

    return '';
  }

  public function moptGetCountriesAssignedToPayment($paymentId)
  {
    $sql    = 'SELECT s_core_paymentmeans_countries.countryID, s_core_countries.countryname, s_core_countries.countryiso '
            . 'FROM s_core_paymentmeans_countries, s_core_countries '
            . 'WHERE s_core_paymentmeans_countries.paymentID = ? '
            . 'AND s_core_countries.id = s_core_paymentmeans_countries.countryID;';
    $paymentCountries = Shopware()->Db()->fetchAll($sql, $paymentId);

    return $paymentCountries;
  }
  
  public function moptGetKlarnaAdditionalInformation($country, $storeId)
  {
    $information = array('consent' => '', 'legalTerm' => '');
    
    switch ($country)
    {
      case 'DE': {
        $information['consent'] = 'Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode '
                . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. '
                . 'Meine <a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_CONSENT_DE . '" '
                . 'style="text-decoration: underline !important;">Einwilligung</a> '
                . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.';
        $information['legalTerm'] = 'Weitere Informationen zum Rechnungskauf finden Sie in den '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DE . '" style="text-decoration: underline !important;">'
                . 'Rechnungsbedingungen</a>.';
      }
        break;
      
      case 'AT': {
        $information['consent'] = 'Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode '
                . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. '
                . 'Meine <a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_CONSENT_AT . '" '
                . 'style="text-decoration: underline !important;">Einwilligung</a> '
                . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.';
        $information['legalTerm'] = 'Weitere Informationen zum Rechnungskauf finden Sie in den '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_AT . '" style="text-decoration: underline !important;">'
                . 'Rechnungsbedingungen</a>.';
      }
        break;
      
      case 'DK': {
        $information['legalTerm'] = 'Accept legal terms. '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                . 'Vilkår for faktura</a>.';
      }
        break;
      
      case 'NL': {
        $information['legalTerm'] = 'Accept legal terms. '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                . 'Factuurvoorwaarden</a>.';
      }
        break;
      
      case 'NO': {
        $information['legalTerm'] = 'Accept legal terms. '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                . 'Vilkår for faktura</a>.';
      }
        break;
      
      case 'FI': {
        $information['legalTerm'] = 'Accept legal terms. '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                . 'Laskuehdot</a>.';
      }
        break;
      
      case 'SE': {
        $information['legalTerm'] = 'Accept legal terms. '
                . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_SE . '" style="text-decoration: underline !important;">'
                . 'Villkor för faktura</a>.';
      }
        break;
    }
    
    $information['consent']   = str_replace('##storeid##', $storeId, $information['consent']);
    $information['legalTerm'] = str_replace('##storeid##', $storeId, $information['legalTerm']);

    return $information;
  }
  
  public function moptUpdateUserInformation($userId, $paymentData)
  {
    $user             = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
    $billing          = $user->getBilling();
    
    if(isset($paymentData['formData']['mopt_payone__klarna_birthyear']))
    {
      $billing->setBirthday($paymentData['formData']['mopt_payone__klarna_birthyear'] 
              . '-' . $paymentData['formData']['mopt_payone__klarna_birthmonth'] 
              . '-' . $paymentData['formData']['mopt_payone__klarna_birthday']);
      $billing->setPhone($paymentData['formData']['mopt_payone__klarna_telephone']);
    }
    else
    {
      $billing->setBirthday($paymentData['formData']['mopt_payone__klarna_inst_birthyear'] 
              . '-' . $paymentData['formData']['mopt_payone__klarna_inst_birthmonth'] 
              . '-' . $paymentData['formData']['mopt_payone__klarna_inst_birthday']);
      $billing->setPhone($paymentData['formData']['mopt_payone__klarna_inst_telephone']);
    }
    Shopware()->Models()->persist($billing);
    Shopware()->Models()->flush();
  }
  
  /**
   * retrieve multilang errorcode based on context and errorcode
   * 
   * @param string $context
   * @param string $errorCode
   * @return string
   */
  public function moptGetErrorMessageFromErrorCodeViaSnippet($context = false, $errorCode = false)
  {
    $namespace = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages');
    if($context)
    {
      $generalErrorMessage = $namespace->get($context . 'ErrorMessage');
    }
    else
    {
      $generalErrorMessage = $namespace->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten');
    }
    
    if($errorCode)
    {
      return $namespace->get('errorMessage' . $errorCode, $generalErrorMessage, true);
    }
    else
    {
      return $generalErrorMessage;
    }
  }
  
  /**
   * collect and return predefinded possible creditcard check error messages
   * 
   * @return array
   */
  public function getCreditCardCheckErrorMessages()
  {
    $errorMessages = array();
    $namespace = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages');
    $errorMessages['general'] = $namespace->get('creditCardCheckerrorMessage', 
            'Bitte überprüfen Sie die Angaben auf der Karte.');
    $errorMessages['1076']    = $namespace->get('errorMessage1076', 
            'Ungültiger Kartentyp. Bitte überprüfen Sie die Angaben auf der Karte.');
    $errorMessages['1078']    = $namespace->get('errorMessage1078', 
            'Ungültige Kartennummer. Bitte überprüfen Sie die Angaben auf der Karte.');
    $errorMessages['33']      = $namespace->get('errorMessage33', 
            'Verfallsdatum ungültig. Bitte überprüfen Sie die Angaben auf der Karte.');
    
    return $errorMessages;
  }
  
  /**
   * get action name from paymentname
   * 
   * @param string $paymentShortName
   * @return string|boolean
   */
    public function getActionFromPaymentName($paymentShortName)
    {
        if ($this->isPayoneCreditcard($paymentShortName))
        {
            return 'creditcard';
        }

        if ($this->isPayoneInstantBankTransfer($paymentShortName))
        {
            return 'instanttransfer';
        }

        if ($this->isPayonePaypal($paymentShortName))
        {
            return 'paypal';
        }

        if ($this->isPayoneDebitnote($paymentShortName))
        {
            return 'debitnote';
        }

        if ($this->isPayoneInvoice($paymentShortName) || $this->isPayonePayInAdvance($paymentShortName))
        {
            return 'standard';
        }

        if ($this->isPayoneCashOnDelivery($paymentShortName))
        {
            return 'cashondel';
        }

        if ($this->isPayoneKlarna($paymentShortName))
        {
            return 'klarna';
        }

        if ($this->isPayoneFinance($paymentShortName))
        {
            return 'finance';
        }

        return false;
    }

}