<div class="debit">
  {if $moptPaymentConfigParams.moptIsSwiss}
    <div class="form-group {if $error_flags.mopt_payone__sofort_bankaccount}has-error{/if}">
        <label for="mopt_payone__sofort_bankaccount" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankAccountNumber'}Kontonummer{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_bankaccount]" type="text" 
                   id="mopt_payone__sofort_bankaccount" value="{$form_data.mopt_payone__sofort_bankaccount|escape}" 
                   class="form-control"/>
        </div>
    </div>

    <div class="form-group {if $error_flags.mopt_payone__sofort_bankcode}has-error{/if}">
        <label for="mopt_payone__sofort_bankcode" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankCode'}Bankleitzahl{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_bankcode]" type="text" id="mopt_payone__sofort_bankcode" 
                   value="{$form_data.mopt_payone__sofort_bankcode|escape}" class="form-control"/>
        </div>
    </div>
  {else}
    <div class="form-group {if $error_flags.mopt_payone__sofort_iban}has-error{/if}">
        <label for="mopt_payone__sofort_iban" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_iban]" type="text" id="mopt_payone__sofort_iban" 
                   value="{$form_data.mopt_payone__sofort_iban|escape}" class="form-control"/>
        </div>
    </div>

    <div class="form-group {if $error_flags.mopt_payone__sofort_bic}has-error{/if}">
        <label for="mopt_payone__sofort_bic" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_bic]" type="text" id="mopt_payone__sofort_bic" 
                   value="{$form_data.mopt_payone__sofort_bic|escape}" class="form-control"/>
        </div>
    </div>
  {/if}

  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" type="text" 
         id="mopt_payone__onlinebanktransfertype" value="PNT"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__sofort_bankcountry]" type="text" 
         id="mopt_payone__sofort_bankcountry" value="{$sUserData.additional.country.countryiso}"/>
</div>

<script type="text/javascript">
  {if $moptPaymentConfigParams.moptIsSwiss}
    $('#mopt_payone__sofort_bankaccount').focus(function () {
        $('#payment_mean{$payment_mean.id}').attr('checked', true);
        $('#moptSavePayment{$payment_mean.id}').slideDown();
        $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $('#mopt_payone__sofort_bankcode').focus(function () {
        $('#payment_mean{$payment_mean.id}').attr('checked', true);
        $('#moptSavePayment{$payment_mean.id}').slideDown();
        $('input[type="radio"]:not(:checked)').trigger('change');
    });
  {else}
    $('#mopt_payone__sofort_iban').focus(function() {
      $('#payment_mean{$payment_mean.id}').attr('checked',true);
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $('#mopt_payone__sofort_bic').focus(function() {
      $('#payment_mean{$payment_mean.id}').attr('checked',true);
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    });
  {/if}
</script>