{extends file="frontend/checkout/confirm.tpl"}

{block name="frontend_index_content" append}
{if $moptAddressCheckNeedsUserVerification}
<script type="text/javascript">
  $(document).ready(function() {
    // Handler for .ready() called.

    $.post('{url controller=moptPaymentPayone action=ajaxVerifyAddress forceSecure}', function (data) {
      $.modal(data, '', {
        'position': 'fixed',
        'width': 500,
        'height': 500
      }).find('.close').remove();
    });
  });
</script>
{/if}
{/block}

{* Payment selection *}
{block name='frontend_checkout_confirm_payment' append}


<script type="text/javascript">
  $(document).ready(function() {
    // Handler for .ready() called.
    
    $('[name="register[payment]"]').removeClass('auto_submit');
    
    var myRadio =  $('input[name="register[payment]"]');
    var orgValue = myRadio.filter('[checked="checked"]').val();
    var orgLabel = $('input[name="register[payment]"]:checked + label').text();
    
    $('#basketButton').closest('form').submit(function() {
      // get orginal checked payment method
      var checkedValue = myRadio.filter(':checked').val();
      var checkedLabel = $('input[name="register[payment]"]:checked + label').text();
      var checkedId = $('input[name="register[payment]"]:checked').attr('id');
      
      if(checkedValue != orgValue)
      {
        //show dialog
        $.post("{url controller=moptPaymentPayone action=ajaxVerifyPayment forceSecure}?moptSelectedPayment="+checkedLabel+"&moptOriginalPayment="+orgLabel+"&moptCheckedId="+checkedId+"", function (data) {
          $.modal(data, '', {
            'position': 'fixed',
            'width': 500,
            'height': 500
          }).find('.close').remove();
        });

        return false;
      }
      else
      {
        $('#basketButton').attr('disabled','disabled');
        return true;
      }
    });
    
  });
</script>


{/block}