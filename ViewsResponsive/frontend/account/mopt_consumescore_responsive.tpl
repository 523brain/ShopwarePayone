{extends file="frontend/account/payment.tpl"}

{block name="frontend_index_content" append}
{if moptConsumerScoreCheckNeedsUserAgreement}

<div id="moptConsumerScoreCheckNeedsUserAgreementModal" 
     class="modal fade" 
     tabindex="-1" 
     role="dialog"
     aria-labelledby="myModalLabel" 
     aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() 
  {
    $.post('{url controller=moptPaymentPayone action=ajaxGetConsumerScoreUserAgreement forceSecure}', function (data) 
    {
      $('#moptConsumerScoreCheckNeedsUserAgreementModal .modal-content').html(data);
      {literal}
      $('#moptConsumerScoreCheckNeedsUserAgreementModal').modal({show:true});
      {/literal}
    });
  });
</script>
{/if}
{/block}