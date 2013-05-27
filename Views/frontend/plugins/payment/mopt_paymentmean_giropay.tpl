<div class="debit">
  <p class="none">
    <label for="mopt_payone__giropay_bankaccount">{*s name='PaymentDebitLabelBankcode'}{/s*}Kontonummer</label>
    <input name="moptPaymentData[mopt_payone__giropay_bankaccount]" type="text" id="mopt_payone__giropay_bankaccount" value="{$form_data.mopt_payone__giropay_bankaccount|escape}" class="text {if $error_flags.mopt_payone__giropay_bankaccount}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__giropay_bankcode">{*s name='PaymentDebitLabelAccount'}{/s*}Bankleitzahl</label>
    <input name="moptPaymentData[mopt_payone__giropay_bankcode]" type="text" id="mopt_payone__giropay_bankcode" value="{$form_data.mopt_payone__giropay_bankcode|escape}" class="text {if $error_flags.mopt_payone__giropay_bankcode}instyle_error{/if}" />
  </p>
  <p class="description">{*s name='PaymentDebitInfoFields'}{/s*}</p>
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" type="text" id="mopt_payone__onlinebanktransfertype" value="GPY"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__giropay_bankcountry]" type="text" id="mopt_payone__giropay_bankcountry" value="DE"/>
</div>
<script type="text/javascript">
  $('#mopt_payone__giropay_bankaccount').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__giropay_bankcode').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
</script>