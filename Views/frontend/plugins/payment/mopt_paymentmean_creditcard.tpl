<div class="debit">
    <p class="mopt_modal_open">
      <a class="button-middle left" style="margin-bottom: 10px;" href="{url controller=moptPaymentPayone action=ajaxCreditCard}" title="Kreditkartendaten eingeben">Kreditkartendaten eingeben</a>
  </p>
  <p class="none {if $error_flags.mopt_payone__cc_accountholder}instyle_error{/if}">
    <label>Karteninhaber</label>
    <input name="moptPaymentData[mopt_payone__cc_accountholder]" readonly type="text" id="mopt_payone__cc_accountholder" class="text {if $error_flags.mopt_payone__cc_accountholder}instyle_error{/if}" value="{$form_data.mopt_payone__cc_accountholder|escape}"/>
  </p>
  <p class="none {if $error_flags.mopt_payone__cc_paymentdescription}instyle_error{/if}">
    <label>Kartentyp</label>
    <input name="moptPaymentData[mopt_payone__cc_paymentdescription]" readonly type="text" id="mopt_payone__cc_paymentdescription" class="text" value="{$form_data.mopt_payone__cc_paymentdescription|escape}"/>
  </p>
  <p class="none {if $error_flags.mopt_payone__cc_truncatedcardpan}instyle_error{/if}">
    <label>Kartennummer</label>
    <input name="moptPaymentData[mopt_payone__cc_truncatedcardpan]" readonly type="text" id="mopt_payone__cc_truncatedcardpan" class="text" value="{$form_data.mopt_payone__cc_truncatedcardpan|escape}"/>
  </p>
  <p class="none {if $error_flags.mopt_payone__cc_month}instyle_error{/if}">
    <label>Gültig bis</label>
    <input name="moptPaymentData[mopt_payone__cc_month]" readonly  style="width: 20px;" type="text" id="mopt_payone__cc_month" class="text" value="{$form_data.mopt_payone__cc_month|escape}"/>
    <input name="moptPaymentData[mopt_payone__cc_year]" readonly  style="width: 50px;" type="text" id="mopt_payone__cc_year" class="text" value="{$form_data.mopt_payone__cc_year|escape}"/>
  </p>
  <input name="moptPaymentData[mopt_payone__cc_pseudocardpan]" type="hidden" id="mopt_payone__cc_pseudocardpan" class="text" value="{$form_data.mopt_payone__cc_pseudocardpan|escape}"/>
  <input name="moptPaymentData[mopt_payone__cc_paymentid]" type="hidden" id="mopt_payone__cc_paymentid" class="text" value="{$form_data.mopt_payone__cc_paymentid|escape}"/>
  <input name="moptPaymentData[mopt_payone__cc_cardtype]" type="hidden" id="mopt_payone__cc_cardtype" class="text" value="{$form_data.mopt_payone__cc_cardtype|escape}"/>
  <input name="moptPaymentData[mopt_payone__cc_paymentname]" type="hidden" id="mopt_payone__cc_paymentname" class="text" value="{$form_data.mopt_payone__cc_paymentname|escape}"/>
  <input name="moptPaymentData[mopt_payone__target]" type="hidden" id="mopt_payone__target" class="text" value="{$sTarget}"/>
  <br />
</div>

{if $form_data.mopt_payone__cc_paymentid}
<script type="text/javascript">
  <!--
  $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_paymentid').val());
  // -->
</script>
{else}
<script type="text/javascript">
  <!--
  $('#payment_meanmopt_payone_creditcard').val({$payment_mean.mopt_payone_credit_cards[0]['id']});
  // -->
</script>
{/if}


{literal}
<script type="text/javascript">
  <!--
  $('.mopt_modal_open a').click(function (event) {
    event.preventDefault();
    $.post(this.href, function (data) {
      $.modal(data, '', {
        'position': 'fixed',
        'width': 500,
        'height': 500
      }).find('.close').remove();
    });
  });
  // -->
</script>
{/literal}