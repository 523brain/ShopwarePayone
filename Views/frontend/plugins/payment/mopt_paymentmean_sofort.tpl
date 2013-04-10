<div class="debit">
  <p class="none">
    <label for="mopt_payone__sofort_bankcountry">{*s name='PaymentDebitLabelName'}{/s*}Land</label>
    <select name="moptPaymentData[mopt_payone__sofort_bankcountry]" id="mopt_payone__sofort_bankcountry" size="1" style="width:auto" class="{if $error_flags.mopt_payone__sofort_bankcountry}instyle_error{/if}">
      <option value="not_choosen">Bitte auswählen...</option>
      <option value="DE" {if $form_data.mopt_payone__sofort_bankcountry == 'DE'}selected="selected"{/if}>Deutschland</option>
      <option value="AT" {if $form_data.mopt_payone__sofort_bankcountry == 'AT'}selected="selected"{/if}>Österreich</option>
      <option value="CH" {if $form_data.mopt_payone__sofort_bankcountry == 'CH'}selected="selected"{/if}>Schweiz</option>
    </select>
  </p>
  <p class="none">
    <label for="mopt_payone__sofort_bankaccount">{*s name='PaymentDebitLabelBankcode'}{/s*}Kontonummer</label>
    <input name="moptPaymentData[mopt_payone__sofort_bankaccount]" type="text" id="mopt_payone__sofort_bankaccount" value="{$form_data.mopt_payone__sofort_bankaccount|escape}" class="text {if $error_flags.mopt_payone__sofort_bankaccount}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__sofort_bankcode">{*s name='PaymentDebitLabelAccount'}{/s*}Bankleitzahl</label>
    <input name="moptPaymentData[mopt_payone__sofort_bankcode]" type="text" id="mopt_payone__sofort_bankcode" value="{$form_data.mopt_payone__sofort_bankcode|escape}" class="text {if $error_flags.mopt_payone__sofort_bankcode}instyle_error{/if}" />
  </p>
  <p class="description">{*s name='PaymentDebitInfoFields'}{/s*}</p>
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" type="text" id="mopt_payone__onlinebanktransfertype" value="PNT"/>
</div>