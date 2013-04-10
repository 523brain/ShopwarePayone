{extends file="frontend/checkout/confirm.tpl"}

{block name="frontend_index_content" append}
{if $moptAddressCheckNeedsUserVerification}
<script type="text/javascript">
  $(document).ready(function() {
    // Handler for .ready() called.

    $.post('{url controller=moptPaymentPayone action=ajaxVerifyAddress}', function (data) {
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