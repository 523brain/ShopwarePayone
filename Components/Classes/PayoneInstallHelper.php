<?php

/**
 * $Id: $
 */
class Mopt_PayoneInstallHelper
{

  /**
   * returns array of PAYONE payment methods
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
            'name'        => 'mopt_payone__fin_klarna',
            'description' => 'PAYONE Klarna',
            'template'    => 'mopt_paymentmean_klarna.tpl',
            'position'    => 21,),
        array(
            'name'        => 'mopt_payone__fin_klarna_installment',
            'description' => 'PAYONE Klarna Ratenkauf',
            'template'    => 'mopt_paymentmean_klarna_installment.tpl',
            'position'    => 22,),
        array(
            'name'        => 'mopt_payone__ibt_p24',
            'description' => 'PAYONE P24',
            'template'    => null,
            'position'    => 23,),
    );
  }

  /**
   * add payment data table 
   */
  public function moptCreatePaymentDataTable()
  {
    $sql = "CREATE TABLE IF NOT EXISTS `s_plugin_mopt_payone_payment_data` (`userId` int(11) NOT NULL,`moptPaymentData`"
            . " text NOT NULL, PRIMARY KEY (`userId`))";
    Shopware()->Db()->exec($sql);
  }

  /**
   * extend config data table with sepa config coloumns
   */
  public function moptExtendConfigDataTable()
  {
    $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN show_accountnumber TINYINT(1) NOT NULL DEFAULT 0,"
            . "ADD COLUMN mandate_active TINYINT(1) NOT NULL DEFAULT 0,"
            . "ADD COLUMN mandate_download_enabled TINYINT(1) NOT NULL DEFAULT 0;";
    Shopware()->Db()->exec($sql);
  }

  /**
   * extend config data table with klarna config coloumn
   */
  public function moptExtendConfigKlarnaDataTable()
  {
    $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN klarna_store_id VARCHAR(255) DEFAULT 0;";
    Shopware()->Db()->exec($sql);
  }

  /**
   * extend config data table with klarna campaing code for installment coloumn
   */
  public function moptExtendConfigKlarnaInstallmentDataTable()
  {
    $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN klarna_campaign_code VARCHAR(255) DEFAULT 0;";
    Shopware()->Db()->exec($sql);
  }

  /**
   * extend config data table with save terms coloumn
   */
  public function moptExtendConfigSaveTermsDataTable()
  {
    $sql = "ALTER TABLE `s_plugin_mopt_payone_config` "
            . "ADD COLUMN save_terms VARCHAR(255) DEFAULT 0;";
    Shopware()->Db()->exec($sql);
  }

  /**
   * insert document extensions (used to display additional PAYONE data) if not already stored in DB
   */
  public function moptInsertDocumentsExtensionIntoDatabaseIfNotExist()
  {
    $sql    = 'SELECT * FROM s_core_documents_box WHERE name = ? OR name = ?';
    $result = Shopware()->Db()->query($sql, array('PAYONE_Footer', 'PAYONE_Content_Info'));

    if ($result->rowCount() < 2)
    {
      // add PAYONE block for documents
      $sql = "INSERT INTO `s_core_documents_box` (`documentID`, `name`, `style`, `value`) VALUES
	(1, 'PAYONE_Footer', 'width: 170mm;\r\nposition:fixed;\r\nbottom:-20mm;\r\nheight: 15mm;', ?),
	(1, 'PAYONE_Content_Info', ?, ?);";
      Shopware()->Db()->query($sql, array(
          '<table style="height: 90px;" border="0" width="100%">'
          . '<tbody>'
          . '<tr valign="top">'
          . '<td style="width: 33%;">'
          . '<p><span style="font-size: xx-small;">Demo GmbH</span></p>'
          . '<p><span style="font-size: xx-small;">Steuer-Nr <br />UST-ID: <br />Finanzamt '
          . '</span><span style="font-size: xx-small;">Musterstadt</span></p>'
          . '</td>'
          . '<td style="width: 33%;">'
          . '<p><span style="font-size: xx-small;">AGB<br /></span></p>'
          . '<p><span style="font-size: xx-small;">Gerichtsstand ist Musterstadt<br />'
          . 'Erf&uuml;llungsort Musterstadt</span></p>'
          . '</td>'
          . '<td style="width: 33%;">'
          . '<p><span style="font-size: xx-small;">Gesch&auml;ftsf&uuml;hrer</span></p>'
          . '<p><span style="font-size: xx-small;">Max Mustermann</span></p>'
          . '</td>'
          . '</tr>'
          . '</tbody>'
          . '</table>',
          '.payment_instruction, .payment_instruction td, .payment_instruction tr {'
          . '	margin: 0;'
          . '	padding: 0;'
          . '	border: 0;'
          . '	font-size:8px;'
          . '	font: inherit;'
          . '	vertical-align: baseline;'
          . '}'
          . '.payment_note {'
          . '	font-size: 10px;'
          . '	color: #333;'
          . '}',
          '<div class="payment_note">'
          . '<br/>'
          . '{$instruction.clearing_instructionnote}<br/>'
          . '{$instruction.clearing_legalnote}}<br/><br/>'
          . '</div>'
          . '<table class="payment_instruction">'
          . '<tr>'
          . '	<td>Empfänger:</td>'
          . '	<td>{$instruction.clearing_bankaccountholder}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>Kontonr.:</td>'
          . '	<td>{$instruction.clearing_bankaccount}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>BLZ:</td>'
          . '	<td>{$instruction.clearing_bankcode}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>IBAN:</td>'
          . '	<td>{$instruction.clearing_bankiban}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>BIC:</td>'
          . '	<td>{$instruction.clearing_bankbic}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>Bank:</td>'
          . '	<td>{$instruction.clearing_bankname}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>Betrag:</td>'
          . '	<td>{$instruction.amount|currency}</td>'
          . '</tr>'
          . '<tr>'
          . '	<td>Verwendungszweck:</td>'
          . '	<td>{$instruction.clearing_reference}</td>'
          . '</tr>'
          . '</table>'
      ));
    }
  }

  /**
   * insert empty config on installations to keep the shop stable
   * without any config and activated plugin exceptions will occur during checkout 
   */
  public function moptInsertEmptyConfigIfNotExists()
  {
    $sql    = 'SELECT id FROM s_plugin_mopt_payone_config WHERE payment_id = 0;';
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      $sql = "INSERT INTO `s_plugin_mopt_payone_config` (`payment_id`, `merchant_id`, `portal_id`, `subaccount_id`, `api_key`, `live_mode`, `authorisation_method`, `submit_basket`, `adresscheck_active`, `adresscheck_live_mode`, `adresscheck_billing_adress`, `adresscheck_shipping_adress`, `adresscheck_automatic_correction`, `adresscheck_failure_handling`, `adresscheck_min_basket`, `adresscheck_max_basket`, `adresscheck_lifetime`, `adresscheck_failure_message`, `map_person_check`, `map_know_pre_lastname`, `map_know_lastname`, `map_not_known_pre_lastname`, `map_multi_name_to_adress`, `map_undeliverable`, `map_person_dead`, `map_wrong_adress`, `consumerscore_active`, `consumerscore_live_mode`, `consumerscore_check_moment`, `consumerscore_check_mode`, `consumerscore_default`, `consumerscore_lifetime`, `consumerscore_min_basket`, `consumerscore_max_basket`, `consumerscore_failure_handling`, `consumerscore_note_message`, `consumerscore_note_active`, `consumerscore_agreement_message`, `consumerscore_agreement_active`, `consumerscore_abtest_value`, `consumerscore_abtest_active`, `payment_specific_data`, `state_appointed`, `state_capture`, `state_paid`, `state_underpaid`, `state_cancelation`, `state_refund`, `state_debit`, `state_reminder`, `state_vauthorization`, `state_vsettlement`, `state_transfer`, `state_invoice`, `check_cc`, `check_account`, `trans_appointed`, `trans_capture`, `trans_paid`, `trans_underpaid`, `trans_cancelation`, `trans_refund`, `trans_debit`, `trans_reminder`, `trans_vauthorization`, `trans_vsettlement`, `trans_transfer`, `trans_invoice`) VALUES
      (0, 0, 0, 0, '0', 0, 'Vorautorisierung', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 'Es ist ein Fehler aufgetreten', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'IH', 0, 0, 0, 0, 0, '', 0, '', 0, 0, 0, 'N;', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, '', '', '', '', '', '', '', '', '', '', '', '');
      ";
      Shopware()->Db()->query($sql);
    }
  }

  /**
   * check if user attributes are already extended
   * 
   * @return boolean 
   */
  public function moptUserAttributesExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_user_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_consumerscore_result'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if billing address attributes are already extended
   *
   * @return boolean 
   */
  public function moptBillingAddressAttributesExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_user_billingaddress_attributes'
               AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_addresscheck_result'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if shipping address attributes are already extended
   *
   * @return boolean 
   */
  public function moptShippingAddressAttributesExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_user_shippingaddress_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_addresscheck_result'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if order attributes are already extended
   *
   * @return boolean 
   */
  public function moptOrderAttributesExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_txid'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if order details attributes are already extended
   *
   * @return boolean 
   */
  public function moptOrderDetailsAttributesExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_details_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_payment_status'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if 2nd order details attributes extension is already made
   * needed to for new shipping costs handling
   *
   * @return boolean 
   */
  public function moptOrderAttributesShippingCostsExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_ship_captured'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if 3rd order attributes extension is already made
   * needed to for abo commerce support
   *
   * @return boolean 
   */
  public function moptOrderAttributesPaymentDataExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_order_attributes'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='mopt_payone_payment_data'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if payone configuration is already extended for sepa options
   *
   * @return boolean 
   */
  public function moptPayoneConfigExtensionExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='show_accountnumber'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if payone configuration is already extended for klarna options
   *
   * @return boolean 
   */
  public function moptPayoneConfigKlarnaExtensionExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='klarna_store_id'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }
  
  /**
   * check if payone configuration is already extended for klarna installment payment
   *
   * @return boolean 
   */
  public function moptPayoneConfigKlarnaInstallmentExtensionExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='klarna_campaign_code'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }
  
  /**
   * check if payone configuration is already extended with save terms option
   *
   * @return boolean 
   */
  public function moptPayoneConfigSaveTermsExtensionExist()
  {
    $DBConfig = Shopware()->Db()->getConfig();
    
    $sql    = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='s_plugin_mopt_payone_config'
                AND TABLE_SCHEMA='". $DBConfig['dbname'] ."'
                AND COLUMN_NAME ='save_terms'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return false;
    }

    return true;
  }

  /**
   * check if the old PAYONE plugin is active and rename label to prevent errors
   *
   * @return string
   */
  public function moptGetConfigurationLabelName()
  {
    $sql    = "SELECT id FROM s_core_menu
                WHERE name = 'Konfiguration'
                AND controller <> 'MoptConfigPayone'";
    $result = Shopware()->Db()->query($sql);

    if ($result->rowCount() === 0)
    {
      return 'Konfiguration';
    }

    return 'PAYONE Konfiguration';
  }
  
}
