{extends file="frontend/account/shipping.tpl"}

{block name="frontend_index_content" append}
{if $moptShippingAddressCheckNeedsUserVerification}
<script type="text/javascript">
  $(document).ready(function() {
    // Handler for .ready() called.

    $.post('{url controller=moptPaymentPayone action=ajaxVerifyShippingAddress}', function (data) {
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