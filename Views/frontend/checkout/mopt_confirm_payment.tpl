{extends file="frontend/register/confirm_payment.tpl"}

{block name="frontend_checkout_payment_fieldset_template" append}
{if $payment_mean.id != 'mopt_payone_creditcard'}
<div id="moptSavePayment{$payment_mean.id}" class="grid_14 bankdata" style="clear: both; margin-right: 0px; margin-left: auto; display: none;">
  <input type="submit" style="float:right; margin-right: 0px;" onclick="MoptSubmitPaymentForm();" class="button-middle large" value="Zahlart speichern"/>
</div>

<script type="text/javascript">
  $('#payment_mean{$payment_mean.id}').change(function() {
    if($('#payment_mean{$payment_mean.id}').attr('checked') == 'checked')
    {
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    }
    else
    {
      $('#moptSavePayment{$payment_mean.id}').slideUp();
    }
      
  });
</script>
{/if}

<script type="text/javascript">
  function MoptSubmitPaymentForm() {
    var forms = $('.payment').get();
    
    $.each(forms, function() {
      var me = this;
      var action = me.getAttribute('action');
      if (action.indexOf("savePayment") >= 0)
      {
        me.submit();  
      }
    });
  };
</script>
{/block}