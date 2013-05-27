<script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>
<script src="{link file='frontend/_resources/javascript/client_api.js'}"></script>

<div class="debit">
  <p class="none {if $error_flags.mopt_payone__cc_accountholder}instyle_error{/if}">
    <label>Karteninhaber</label>
    <input name="moptPaymentData[mopt_payone__cc_accountholder]" type="text" id="mopt_payone__cc_accountholder" class="text {if $error_flags.mopt_payone__cc_accountholder}instyle_error{/if}" value="{$form_data.mopt_payone__cc_accountholder|escape}"/>
  </p>
  <p class="none">
    <label for="mopt_payone__cc_cardtype">Kartentyp</label>
    <select name="moptPaymentData[mopt_payone__cc_cardtype]" id="mopt_payone__cc_cardtype" size="1" style="width:auto">
      <option value="not_choosen">Bitte auswählen...</option>
      {foreach from=$moptCreditCardCheckEnvironment.payment_mean.mopt_payone_credit_cards item=credit_card}
      <option value="{$credit_card.short}" {if $form_data.mopt_payone__cc_paymentname == $credit_card.name}selected="selected"{/if} mopt_payone__cc_paymentname="{$credit_card.name}" mopt_payone__cc_paymentid="{$credit_card.id}">{$credit_card.description}</option>
      {/foreach}
    </select>
  </p>
  <p class="none {if $error_flags.mopt_payone__cc_truncatedcardpan}instyle_error{/if}">
    <label>Kartennummer</label>
    <input name="moptPaymentData[mopt_payone__cc_truncatedcardpan]" type="text" id="mopt_payone__cc_truncatedcardpan" class="text" value="{$form_data.mopt_payone__cc_truncatedcardpan|escape}"/>
  </p>
  <p class="none">
    <label for="mopt_payone__cc_month">Gültig Bis</label>
    <select name="moptPaymentData[mopt_payone__cc_month]" id="mopt_payone__cc_month" size="1" style="width:auto">
      <option {if $form_data.mopt_payone__cc_month == '01'}selected="selected"{/if} value="01">01</option>
      <option {if $form_data.mopt_payone__cc_month == '02'}selected="selected"{/if} value="02">02</option>
      <option {if $form_data.mopt_payone__cc_month == '03'}selected="selected"{/if} value="03">03</option>
      <option {if $form_data.mopt_payone__cc_month == '04'}selected="selected"{/if} value="04">04</option>
      <option {if $form_data.mopt_payone__cc_month == '05'}selected="selected"{/if} value="05">05</option>
      <option {if $form_data.mopt_payone__cc_month == '06'}selected="selected"{/if} value="06">06</option>
      <option {if $form_data.mopt_payone__cc_month == '07'}selected="selected"{/if} value="07">07</option>
      <option {if $form_data.mopt_payone__cc_month == '08'}selected="selected"{/if} value="08">08</option>
      <option {if $form_data.mopt_payone__cc_month == '09'}selected="selected"{/if} value="09">09</option>
      <option {if $form_data.mopt_payone__cc_month == '10'}selected="selected"{/if} value="10">10</option>
      <option {if $form_data.mopt_payone__cc_month == '11'}selected="selected"{/if} value="11">11</option>
      <option {if $form_data.mopt_payone__cc_month == '12'}selected="selected"{/if} value="12">12</option>
    </select>
    {html_select_date prefix='mopt_payone__cc_' end_year='+10' display_days=false display_months=false year_extra='style="width:auto" id="mopt_payone__cc_Year"'}
  </p>
  <p class="none" {if !$moptCreditCardCheckEnvironment.moptPayoneCheckCc}style="display:none;"{/if}>
     <label for="mopt_payone__cc_cvc">Prüfziffer </label>
    <input name="mopt_payone__cc_cvc" type="text" id="mopt_payone__cc_cvc" class="text" />
  </p>

  <input name="moptPaymentData[mopt_payone__cc_pseudocardpan]" type="hidden" id="mopt_payone__cc_pseudocardpan" class="text" value="{$form_data.mopt_payone__cc_pseudocardpan|escape}"/>
  <input name="moptPaymentData[mopt_payone__cc_paymentid]" type="hidden" id="mopt_payone__cc_paymentid" class="text" value="{$form_data.mopt_payone__cc_paymentid|escape}"/>
  <input name="moptPaymentData[mopt_payone__cc_paymentname]" type="hidden" id="mopt_payone__cc_paymentname" class="text" value="{$form_data.mopt_payone__cc_paymentname|escape}"/>
  <input name="moptPaymentData[mopt_payone__cc_paymentdescription]" type="hidden" id="mopt_payone__cc_paymentdescription" class="text" value="{$form_data.mopt_payone__cc_paymentdescription|escape}"/>
  <br />
</div>

<div id="moptSavePayment{$payment_mean.id}" class="grid_9 bankdata" style="clear: both; margin-right: 0px; margin-left: auto; display: none;">
  <input type="submit" style="float:right; margin-right: 0px;" onclick="checkCreditCard(); return false;" class="button-middle large" value="Zahlart speichern"/>
</div>

<script type="text/javascript">
  <!--
  
  {if $form_data.mopt_payone__cc_paymentid}
  $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_paymentid').val());
  {else}
  $('#payment_meanmopt_payone_creditcard').val({$payment_mean.mopt_payone_credit_cards[0]['id']});
  {/if}
  
  {if $form_data.mopt_payone__cc_Year}
  $('#mopt_payone__cc_Year').val('{$form_data.mopt_payone__cc_Year}');
  {/if}
  
  $('#payment_mean{$payment_mean.id}').change(function() {
    if($('#payment_mean{$payment_mean.id}').attr('checked') == 'checked')
    {
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    }
    else
    {
      $('#moptSavePayment{$payment_mean.id}').slideUp();
    }
  });
  
  $('#mopt_payone__cc_accountholder').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_cardtype').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_truncatedcardpan').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_month').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_Year').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_cvc').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  function checkCreditCard() {
    var data = {
      request : 'creditcardcheck',
      mode : '{$moptCreditCardCheckEnvironment.moptPayoneParams.mode}',
      mid : '{$moptCreditCardCheckEnvironment.moptPayoneParams.mid}',
      aid : '{$moptCreditCardCheckEnvironment.moptPayoneParams.aid}',
      portalid : '{$moptCreditCardCheckEnvironment.moptPayoneParams.portalid}',
      encoding : 'UTF-8',
      storecarddata : 'yes',
      hash : '{$moptCreditCardCheckEnvironment.moptPayoneParams.hash}',
      cardholder : $('#mopt_payone__cc_accountholder').val(),
      cardpan : $('#mopt_payone__cc_truncatedcardpan').val(),
      cardtype: $('#mopt_payone__cc_cardtype').val(),
      cardexpiremonth : $('#mopt_payone__cc_month').val(),
      cardexpireyear : $('#mopt_payone__cc_Year').val(),
      cardcvc2 : $('#mopt_payone__cc_cvc').val(),
      language : '{$moptCreditCardCheckEnvironment.moptPayoneParams.language}',
      responsetype : 'JSON'
    }
    var options = {
      return_type : 'object',
      callback_function_name : 'processPayoneResponse'
    }
    var request = new PayoneRequest(data, options);
    $('#mopt_payone__cc_cvc').val('');
    request.checkAndStore();
  }
  
  function processPayoneResponse(response) {
    if (response.get('status') == 'VALID') {
      $('#mopt_payone__cc_paymentdescription').val($('#mopt_payone__cc_cardtype option:selected').text());
      $('#mopt_payone__cc_truncatedcardpan').val(response.get('truncatedcardpan'));
      $('#mopt_payone__cc_pseudocardpan').val(response.get('pseudocardpan'));
      $('#mopt_payone__cc_paymentid').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
      $('#mopt_payone__cc_paymentname').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'));
      $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
      
      var data = {
        mopt_payone__cc_truncatedcardpan : response.get('truncatedcardpan'),
        mopt_payone__cc_month : $('#mopt_payone__cc_month').val(),
        mopt_payone__cc_Year : $('#mopt_payone__cc_Year').val(),
        mopt_payone__cc_pseudocardpan : response.get('pseudocardpan'),
        mopt_payone__cc_paymentname : $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'),
        mopt_payone__cc_paymentid : $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'),
        mopt_payone__cc_paymentdescription : $('#mopt_payone__cc_cardtype option:selected').text()
      }
      jQuery.post( '{url controller="moptPaymentPayone" action="savePseudoCard"}' , data ,function() {
        MoptSubmitPaymentForm();
      });
    }
    else {
      alert(response.get('customermessage'));
    }
  }
  // -->
</script>