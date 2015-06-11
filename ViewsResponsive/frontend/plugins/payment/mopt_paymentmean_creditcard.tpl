<script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>
<script src="{link file='frontend/_resources/javascript/client_api.js'}"></script>

<div class="debit">
  <div class="form-group {if $error_flags.mopt_payone__cc_accountholder}has-error{/if}">
    <label class="col-lg-4 control-label">
      {s namespace='frontend/MoptPaymentPayone/payment' name='creditCardHolder'}Karteninhaber{/s}
    </label>

    <div class="col-lg-6">
      <input name="moptPaymentData[mopt_payone__cc_accountholder]" type="text" 
             id="mopt_payone__cc_accountholder" class="form-control"
             value="{$form_data.mopt_payone__cc_accountholder|escape}"/>
    </div>
  </div>
  <div class="form-group">
    <label for="mopt_payone__cc_cardtype" class="col-lg-4 control-label">
      {s namespace='frontend/MoptPaymentPayone/payment' name='creditCardType'}Kartentyp{/s}
    </label>

    <div class="col-lg-6">
      <select name="moptPaymentData[mopt_payone__cc_cardtype]" 
              id="mopt_payone__cc_cardtype" size="1" class="form-control">
        <option value="not_choosen">
          {s namespace='frontend/MoptPaymentPayone/payment' name='selectValueLabel'}Bitte auswählen...{/s}
        </option>
        {foreach from=$moptCreditCardCheckEnvironment.payment_mean.mopt_payone_credit_cards item=credit_card}
        <option value="{$credit_card.short}" 
                {if $form_data.mopt_payone__cc_paymentname == $credit_card.name}selected="selected"{/if} 
                mopt_payone__cc_paymentname="{$credit_card.name}" 
                mopt_payone__cc_paymentid="{$credit_card.id}">{$credit_card.description}
      </option>
      {/foreach}
    </select>
  </div>
</div>
<div class="form-group {if $error_flags.mopt_payone__cc_truncatedcardpan}has-error{/if}">
  <label class="col-lg-4 control-label">
    {s namespace='frontend/MoptPaymentPayone/payment' name='creditCardNumber'}Kartennummer{/s}
  </label>

  <div class="col-lg-6">
    <input name="moptPaymentData[mopt_payone__cc_truncatedcardpan]" type="text" 
           id="mopt_payone__cc_truncatedcardpan" class="form-control" 
           value="{$form_data.mopt_payone__cc_truncatedcardpan|escape}"/>
  </div>
</div>

<div class="form-group">
  <label class="col-lg-4 control-label" for="mopt_payone__cc_month">
     {s namespace='frontend/MoptPaymentPayone/payment' name='creditCardValidUntil'}Gültig Bis{/s}
  </label>

  <div class="col-lg-6">
    <div class="row">
      <div class="col-md-3 col-sm-6">
        <select name="moptPaymentData[mopt_payone__cc_month]" 
                id="mopt_payone__cc_month" size="1" class="form-control">
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
      </div>
      <div class="col-md-4 col-sm-6">
        {html_select_date prefix='mopt_payone__cc_' end_year='+10' display_days=false 
        display_months=false year_extra='class="form-control" id="mopt_payone__cc_Year"'}
      </div>
    </div>
  </div>
</div>
<div class="none" {if !$moptCreditCardCheckEnvironment.moptPayoneCheckCc}style="display:none;"{/if}>
     <div class="form-group">
    <label class="col-lg-4 control-label" for="mopt_payone__cc_cvc">
      {s namespace='frontend/MoptPaymentPayone/payment' name='creditCardCvc'}Prüfziffer{/s}
    </label>
    <div class="col-md-2 col-xs-6">
      <input name="mopt_payone__cc_cvc" type="text" id="mopt_payone__cc_cvc" class="form-control"/>
    </div>
  </div>
</div>
<input name="moptPaymentData[mopt_payone__cc_pseudocardpan]" type="hidden" id="mopt_payone__cc_pseudocardpan" 
       value="{$form_data.mopt_payone__cc_pseudocardpan|escape}"/>
<input name="moptPaymentData[mopt_payone__cc_paymentid]" type="hidden" id="mopt_payone__cc_paymentid" 
       value="{$form_data.mopt_payone__cc_paymentid|escape}"/>
<input name="moptPaymentData[mopt_payone__cc_paymentname]" type="hidden" id="mopt_payone__cc_paymentname" 
       value="{$form_data.mopt_payone__cc_paymentname|escape}"/>
<input name="moptPaymentData[mopt_payone__cc_paymentdescription]" type="hidden" id="mopt_payone__cc_paymentdescription" 
       value="{$form_data.mopt_payone__cc_paymentdescription|escape}"/>


<div id="moptSavePayment{$payment_mean.id}" style="display: none;">
  <div class="form-group">
    <div class="col-lg-offset-4 col-lg-8">

      <input type="submit" style="" onclick="checkCreditCard(); return false;" class="btn btn-primary" 
        value="{s namespace='frontend/MoptPaymentPayone/payment' name='savePayment'}Zahlart speichern{/s}"/>
    </div>
  </div>
</div>
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

  $(document).ready(function()
  {
    if($('#mopt_payone__cc_truncatedcardpan').val().indexOf("xxxx") >= 0)
    {
      $('#mopt_payone__cc_cvc').val("{s namespace='frontend/MoptPaymentPayone/payment' name='creditCardCvcProcessed'}Kartenprüfziffer wurde verarbeitet{/s}");
    }
  });

  $('#payment_mean{$payment_mean.id}').change(function () 
  {
    if ($('#payment_mean{$payment_mean.id}').attr('checked') == 'checked') 
    {
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    }
    else 
    {
      $('#moptSavePayment{$payment_mean.id}').slideUp();
    }
  });
$('#mopt_payone__cc_accountholder').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked', true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_cardtype').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked', true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_truncatedcardpan').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked', true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_month').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked', true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_Year').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked', true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__cc_cvc').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked', true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  function checkCreditCard() 
  {
      
  var numberReg =  /^[0-9]+$/;
  var formNotValid = false;

  $(".moptFormError").remove();
  $('#mopt_payone__cc_truncatedcardpan').removeClass('instyle_error');
  $('#mopt_payone__cc_cvc').removeClass('instyle_error');

  if(!numberReg.test($('#mopt_payone__cc_truncatedcardpan').val())){
        $('#mopt_payone__cc_truncatedcardpan').addClass('instyle_error');
        $('#mopt_payone__cc_truncatedcardpan').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}</div>');
        formNotValid = true;
    }
  if(!numberReg.test($('#mopt_payone__cc_cvc').val())){
        $('#mopt_payone__cc_cvc').addClass('instyle_error');
        $('#mopt_payone__cc_cvc').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}</div>');
        formNotValid = true;
    }

  if(formNotValid)
  {
      return false;
  }
  
  var data = 
  {
    request: 'creditcardcheck',
    mode: '{$moptCreditCardCheckEnvironment.moptPayoneParams.mode}',
    mid: '{$moptCreditCardCheckEnvironment.moptPayoneParams.mid}',
    aid: '{$moptCreditCardCheckEnvironment.moptPayoneParams.aid}',
    portalid: '{$moptCreditCardCheckEnvironment.moptPayoneParams.portalid}',
    encoding: 'UTF-8',
    storecarddata: 'yes',
    hash: '{$moptCreditCardCheckEnvironment.moptPayoneParams.hash}',
    cardholder: $('#mopt_payone__cc_accountholder').val(),
    cardpan: $('#mopt_payone__cc_truncatedcardpan').val(),
    cardtype: $('#mopt_payone__cc_cardtype').val(),
    cardexpiremonth: $('#mopt_payone__cc_month').val(),
    cardexpireyear: $('#mopt_payone__cc_Year').val(),
    cardcvc2: $('#mopt_payone__cc_cvc').val(),
    language: '{$moptCreditCardCheckEnvironment.moptPayoneParams.language}',
    responsetype: 'JSON'
  }
  var options = {
  return_type: 'object',
          callback_function_name: 'processPayoneResponse'
  }
  var request = new PayoneRequest(data, options);
          $('#mopt_payone__cc_cvc').val('');
          request.checkAndStore();
  }

  function processPayoneResponse(response) 
  {
    if (response.get('status') == 'VALID') 
    {
    $('#mopt_payone__cc_paymentdescription').val($('#mopt_payone__cc_cardtype option:selected').text());
    $('#mopt_payone__cc_truncatedcardpan').val(response.get('truncatedcardpan'));
    $('#mopt_payone__cc_pseudocardpan').val(response.get('pseudocardpan'));
    $('#mopt_payone__cc_paymentid').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
    $('#mopt_payone__cc_paymentname').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'));
    $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
    $('#mopt_payone__cc_cvc').val("{s namespace='frontend/MoptPaymentPayone/payment' name='creditCardCvcProcessed'}Kartenprüfziffer wurde verarbeitet{/s}");
    var data = {
      mopt_payone__cc_truncatedcardpan: response.get('truncatedcardpan'),
      mopt_payone__cc_month: $('#mopt_payone__cc_month').val(),
      mopt_payone__cc_Year: $('#mopt_payone__cc_Year').val(),
      mopt_payone__cc_pseudocardpan: response.get('pseudocardpan'),
      mopt_payone__cc_paymentname: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'),
      mopt_payone__cc_paymentid: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'),
      mopt_payone__cc_paymentdescription: $('#mopt_payone__cc_cardtype option:selected').text()
    }
    jQuery.post('{url controller="moptPaymentPayone" action="savePseudoCard" forceSecure}', data, function () 
    {
    MoptSubmitPaymentForm();
    });
    }
    else 
    {
      var errorMessages = [{$moptCreditCardCheckEnvironment.moptPayoneParams.errorMessages}];
      var errorCode = response.get('errorcode');
      if(errorCode in errorMessages[0])
      { 
        alert(errorMessages[0][errorCode]);
      }
      else
      {
        alert(errorMessages[0].general);
      }
    }
  }
  // -->
</script>