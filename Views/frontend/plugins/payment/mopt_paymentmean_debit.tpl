<div class="debit">
  <p class="none">
    <label for="mopt_payone__debit_bankaccount">{*s name='PaymentDebitLabelAccount'}{/s*}Kontonummer</label>
    <input name="moptPaymentData[mopt_payone__debit_bankaccount]" type="text" id="mopt_payone__debit_bankaccount" value="{$form_data.mopt_payone__debit_bankaccount|escape}" class="text {if $error_flags.mopt_payone__debit_bankaccount}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__debit_bankcode">{*s name='PaymentDebitLabelBankcode'}{/s*}Bankleitzahl</label>
    <input name="moptPaymentData[mopt_payone__debit_bankcode]" type="text" id="mopt_payone__debit_bankcode" value="{$form_data.mopt_payone__debit_bankcode|escape}" class="text {if $error_flags.mopt_payone__debit_bankcode}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__debit_bankaccountholder">{*s name='PaymentDebitLabelBankcode'}{/s*}Kontoinhaber</label>
    <input name="moptPaymentData[mopt_payone__debit_bankaccountholder]" type="text" id="mopt_payone__debit_bankaccountholder" value="{$form_data.mopt_payone__debit_bankaccountholder|escape}" class="text {if $error_flags.mopt_payone__debit_bankaccountholder}instyle_error{/if}" />
  </p>
  <p class="description">{*s name='PaymentDebitInfoFields'}{/s*}</p>
  <input type="hidden" name="moptPaymentData[mopt_payone__debit_bankcountry]" type="text" id="mopt_payone__debit_bankcountry" value="{$sUserData.additional.country.countryiso}"/>
</div>