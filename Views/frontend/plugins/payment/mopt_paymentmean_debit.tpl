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
  <p class="none">
    <label for="mopt_payone__debit_bankcountry">{*s name='PaymentDebitLabelName'}{/s*}Land</label>
    <select name="moptPaymentData[mopt_payone__debit_bankcountry]" id="mopt_payone__debit_bankcountry" size="1" style="width:auto" class="{if $error_flags.mopt_payone__debit_bankcountry}instyle_error{/if}">
      <option value="not_choosen">Bitte auswählen...</option>
      <option value="DE" {if $form_data.mopt_payone__debit_bankcountry == 'DE'}selected="selected"{/if}>Deutschland</option>
      <option value="AT" {if $form_data.mopt_payone__debit_bankcountry == 'AT'}selected="selected"{/if}>Österreich</option>
      <option value="CH" {if $form_data.mopt_payone__debit_bankcountry == 'CH'}selected="selected"{/if}>Schweiz</option>
    </select>
  </p>
  <p class="description">{*s name='PaymentDebitInfoFields'}{/s*}</p>
</div>