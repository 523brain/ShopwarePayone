<div class="debit">
  <p class="none">
    <label for="mopt_payone__sofort_bankaccount">{*s name='PaymentDebitLabelBankcode'}{/s*}Kontonummer</label>
    <input name="moptPaymentData[mopt_payone__sofort_bankaccount]" type="text" id="mopt_payone__sofort_bankaccount" value="{$form_data.mopt_payone__sofort_bankaccount|escape}" class="text {if $error_flags.mopt_payone__sofort_bankaccount}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__sofort_bankcode">{*s name='PaymentDebitLabelAccount'}{/s*}Bankleitzahl</label>
    <input name="moptPaymentData[mopt_payone__sofort_bankcode]" type="text" id="mopt_payone__sofort_bankcode" value="{$form_data.mopt_payone__sofort_bankcode|escape}" class="text {if $error_flags.mopt_payone__sofort_bankcode}instyle_error{/if}" />
  </p>
  <p class="description">{*s name='PaymentDebitInfoFields'}{/s*}<br>
  </p>
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" type="text" id="mopt_payone__onlinebanktransfertype" value="PNT"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__sofort_bankcountry]" type="text" id="mopt_payone__sofort_bankcountry" value="{$sUserData.additional.country.countryiso}"/>
</div>