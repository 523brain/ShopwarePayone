{extends file="frontend/account/payment.tpl"}

{block name="frontend_index_content" append}
{if moptConsumerScoreCheckNeedsUserAgreement}
<script type="text/javascript">
  $(document).ready(function() {
    // Handler for .ready() called.

    $.post('{url controller=moptPaymentPayone action=ajaxGetConsumerScoreUserAgreement}', function (data) {
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