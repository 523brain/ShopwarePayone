<div class="debit">
  <p class="none">
    <label for="mopt_payone__ideal_bankgrouptype">{*s name='PaymentDebitLabelName'}{/s*}Bankgruppe</label>
    <select name="moptPaymentData[mopt_payone__ideal_bankgrouptype]" id="mopt_payone__ideal_bankgrouptype" size="1" style="width:auto" class="{if $error_flags.mopt_payone__ideal_bankgrouptype}instyle_error{/if}">
      <option value="not_choosen">Bitte auswählen...</option>
      <option value="ABN_AMRO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ABN_AMRO_BANK'}selected="selected"{/if}>ABN AMRO</option>
      <option value="FORTIS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'FORTIS_BANK'}selected="selected"{/if}>Fortis</option>
      <option value="FRIESLAND_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'FRIESLAND_BANK'}selected="selected"{/if}>Friesland Bank</option>
      <option value="ING_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ING_BANK'}selected="selected"{/if}>ING</option>
      <option value="RABOBANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'RABOBANK'}selected="selected"{/if}>Rabobank</option>
      <option value="SNS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_BANK'}selected="selected"{/if}>SNS BANK</option>
      <option value="ASN_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ASN_BANK'}selected="selected"{/if}>ASN Bank</option>
      <option value="SNS_REGIO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_REGIO_BANK'}selected="selected"{/if}>SNS Regio Bank</option>
      <option value="TRIODOS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'TRIODOS_BANK'}selected="selected"{/if}>Triodos Bank</option>
    </select>
  </p>
  <p class="description">{*s name='PaymentDebitInfoFields'}{/s*}</p>
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" type="text" id="mopt_payone__onlinebanktransfertype" value="IDL"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__ideal_bankcountry]" type="text" id="mopt_payone__ideal_bankcountry" value="NL"/>
</div>