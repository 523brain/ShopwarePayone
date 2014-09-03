<div class="debit">
  {if $moptPaymentConfigParams.moptIsSwiss}
    <p class="none">
      <label for="mopt_payone__sofort_bankaccount">
        {s namespace='frontend/MoptPaymentPayone/payment' name='bankAccountNumber'}Kontonummer{/s}
      </label>
      <input name="moptPaymentData[mopt_payone__sofort_bankaccount]" type="text" id="mopt_payone__sofort_bankaccount" 
             value="{$form_data.mopt_payone__sofort_bankaccount|escape}" 
             class="text {if $error_flags.mopt_payone__sofort_bankaccount}instyle_error{/if}" />
    </p>
    <p class="none">
      <label for="mopt_payone__sofort_bankcode">
        {s namespace='frontend/MoptPaymentPayone/payment' name='bankCode'}Bankleitzahl{/s}
      </label>
      <input name="moptPaymentData[mopt_payone__sofort_bankcode]" type="text" id="mopt_payone__sofort_bankcode" 
             value="{$form_data.mopt_payone__sofort_bankcode|escape}" 
             class="text {if $error_flags.mopt_payone__sofort_bankcode}instyle_error{/if}" />
    </p>
  {else}
    <p class="none">
      <label for="mopt_payone__sofort_iban">
        {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
      </label>
      <input name="moptPaymentData[mopt_payone__sofort_iban]" type="text" id="mopt_payone__sofort_iban" 
             value="{$form_data.mopt_payone__sofort_iban|escape}" 
             class="text {if $error_flags.mopt_payone__sofort_iban}instyle_error{/if}" />
    </p>
    <p class="none">
      <label for="mopt_payone__sofort_bic">
        {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
      </label>
      <input name="moptPaymentData[mopt_payone__sofort_bic]" type="text" id="mopt_payone__sofort_bic" 
             value="{$form_data.mopt_payone__sofort_bic|escape}" 
             class="text {if $error_flags.mopt_payone__sofort_bic}instyle_error{/if}" />
    </p>
  {/if}
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" 
         type="text" id="mopt_payone__onlinebanktransfertype" value="PNT"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__sofort_bankcountry]" 
         type="text" id="mopt_payone__sofort_bankcountry" value="{$sUserData.additional.country.countryiso}"/>
</div>

<script type="text/javascript">
  {if $moptPaymentConfigParams.moptIsSwiss}
    $('#mopt_payone__sofort_bankaccount').focus(function() {
      $('#payment_mean{$payment_mean.id}').attr('checked',true);
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $('#mopt_payone__sofort_bankcode').focus(function() {
      $('#payment_mean{$payment_mean.id}').attr('checked',true);
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