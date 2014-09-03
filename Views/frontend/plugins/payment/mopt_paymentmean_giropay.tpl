<div class="debit">
  <p class="none">
    <label for="mopt_payone__giropay_iban">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__giropay_iban]" type="text" id="mopt_payone__giropay_iban" 
           value="{$form_data.mopt_payone__giropay_iban|escape}" 
           class="text {if $error_flags.mopt_payone__giropay_iban}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__giropay_bic">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__giropay_bic]" type="text" id="mopt_payone__giropay_bic" 
           value="{$form_data.mopt_payone__giropay_bic|escape}" 
           class="text {if $error_flags.mopt_payone__giropay_bic}instyle_error{/if}" />
  </p>
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" type="text" 
         id="mopt_payone__onlinebanktransfertype" value="GPY"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__giropay_bankcountry]" type="text" 
         id="mopt_payone__giropay_bankcountry" value="DE"/>
</div>

<script type="text/javascript">
  $('#mopt_payone__giropay_iban').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__giropay_bic').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
</script>