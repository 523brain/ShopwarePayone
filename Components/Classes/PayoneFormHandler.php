<?php

/**
 * $Id: $
 */
class Mopt_PayoneFormHandler
{

  public function processPaymentForm($paymentId, $formData)
  {
    $paymentData = array();
    switch ($paymentId)
    {
      case 'mopt_payone__ibt_sofortueberweisung':
        $paymentData = $this->proccessSofortueberweisung($formData);
        break;
      case 'mopt_payone__ibt_giropay':
        $paymentData = $this->proccessGiropay($formData);
        break;
      case 'mopt_payone__ibt_eps':
        $paymentData = $this->proccessEps($formData);
        break;
      case 'mopt_payone__ibt_ideal':
        $paymentData = $this->proccessIdeal($formData);
        break;
      case 'mopt_payone__acc_debitnote':
        $paymentData = $this->proccessDebitNote($formData);
        break;
      case 'mopt_payone_creditcard':
        $paymentData = $this->proccessCreditCard($formData);
        break;
    }

    if (preg_match('#mopt_payone__cc#', $paymentId))
    {
      $paymentData = $this->proccessCreditCard($formData);
    }

    return $paymentData;
  }

  protected function proccessSofortueberweisung($formData)
  {
    $paymentData = array();

    if ($formData["mopt_payone__sofort_bankcountry"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bankcountry"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bankcountry"] = $formData["mopt_payone__sofort_bankcountry"];
    }
    if (!$formData["mopt_payone__sofort_bankaccount"])
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bankaccount"] = $formData["mopt_payone__sofort_bankaccount"];
    }
    if (!$formData["mopt_payone__sofort_bankcode"])
    {
      $paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__sofort_bankcode"] = $formData["mopt_payone__sofort_bankcode"];
    }

    if (count($paymentData['sErrorFlag']))
    {
      return $paymentData;
    }

    $paymentData['formData']['onlinebanktransfertype'] = 'PNT';

    return $paymentData;
  }

  protected function proccessGiropay($formData)
  {
    $paymentData = array();

    if (!$formData["mopt_payone__giropay_bankaccount"])
    {
      $paymentData['sErrorFlag']["mopt_payone__giropay_bankaccount"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__giropay_bankaccount"] = $formData["mopt_payone__giropay_bankaccount"];
    }
    if (!$formData["mopt_payone__giropay_bankcode"])
    {
      $paymentData['sErrorFlag']["mopt_payone__giropay_bankcode"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__giropay_bankcode"] = $formData["mopt_payone__giropay_bankcode"];
    }

    if (count($paymentData['sErrorFlag']))
    {
      return $paymentData;
    }

    $paymentData['formData']['onlinebanktransfertype']           = 'GPY';
    $paymentData['formData']['mopt_payone__giropay_bankcountry'] = 'DE';

    return $paymentData;
  }

  protected function proccessEps($formData)
  {
    $paymentData = array();

    if ($formData["mopt_payone__eps_bankgrouptype"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__eps_bankgrouptype"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__eps_bankgrouptype"] = $formData["mopt_payone__eps_bankgrouptype"];
      $paymentData['formData']['onlinebanktransfertype']         = 'EPS';
      $paymentData['formData']['mopt_payone__eps_bankcountry']   = 'AT';
    }

    return $paymentData;
  }

  protected function proccessIdeal($formData)
  {
    $paymentData = array();

    if ($formData["mopt_payone__ideal_bankgrouptype"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__ideal_bankgrouptype"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__ideal_bankgrouptype"] = $formData["mopt_payone__ideal_bankgrouptype"];
      $paymentData['formData']['onlinebanktransfertype']           = 'EPS';
      $paymentData['formData']['mopt_payone__ideal_bankcountry']   = 'NL';
    }

    return $paymentData;
  }

  protected function proccessDebitNote($formData)
  {
    $paymentData = array();

    if (!$formData["mopt_payone__debit_bankaccount"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankaccount"] = $formData["mopt_payone__debit_bankaccount"];
    }
    if (!$formData["mopt_payone__debit_bankcode"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankcode"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankcode"] = $formData["mopt_payone__debit_bankcode"];
    }
    if (!$formData["mopt_payone__debit_bankaccountholder"])
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankaccountholder"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankaccountholder"] = $formData["mopt_payone__debit_bankaccountholder"];
    }
    if ($formData["mopt_payone__debit_bankcountry"] == 'not_choosen')
    {
      $paymentData['sErrorFlag']["mopt_payone__debit_bankcountry"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__debit_bankcountry"] = $formData["mopt_payone__debit_bankcountry"];
    }

    return $paymentData;
  }

  protected function proccessCreditCard($formData)
  {
    $paymentData = array();

    if (!$formData["mopt_payone__cc_truncatedcardpan"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_truncatedcardpan"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_truncatedcardpan"] = $formData["mopt_payone__cc_truncatedcardpan"];
    }

    if (!$formData["mopt_payone__cc_pseudocardpan"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_pseudocardpan"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_pseudocardpan"] = $formData["mopt_payone__cc_pseudocardpan"];
    }

    if (!$formData["mopt_payone__cc_cardtype"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_cardtype"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_cardtype"] = $formData["mopt_payone__cc_cardtype"];
    }

    if (!$formData["mopt_payone__cc_accountholder"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_accountholder"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_accountholder"] = $formData["mopt_payone__cc_accountholder"];
    }

    if (!$formData["mopt_payone__cc_month"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_month"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_month"] = $formData["mopt_payone__cc_month"];
    }

    if (!$formData["mopt_payone__cc_year"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_year"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_year"] = $formData["mopt_payone__cc_year"];
    }

    if (!$formData["mopt_payone__cc_paymentname"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_paymentname"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_paymentname"] = $formData["mopt_payone__cc_paymentname"];
    }

    if (!$formData["mopt_payone__cc_paymentid"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_paymentid"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_paymentid"] = $formData["mopt_payone__cc_paymentid"];
    }

    if (!$formData["mopt_payone__cc_paymentdescription"])
    {
      $paymentData['sErrorFlag']["mopt_payone__cc_paymentdescription"] = true;
    }
    else
    {
      $paymentData['formData']["mopt_payone__cc_paymentdescription"] = $formData["mopt_payone__cc_paymentdescription"];
    }

    return $paymentData;
  }

}