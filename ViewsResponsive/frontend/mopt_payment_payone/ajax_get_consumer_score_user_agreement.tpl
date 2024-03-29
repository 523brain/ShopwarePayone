<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="myModalLabel">
    {s namespace='frontend/MoptPaymentPayone/payment' name='confirmConsumerScoreCheckTitle'}Bonitätsprüfung Bestätigen{/s}
  </h4>
</div>
<div class="modal-body">
  <p style="padding: 0 25px 0 0;" class="none">
    {$consumerscoreNoteMessage}
  </p>
  <p style="padding: 25px 0 25px 0;" class="none">
    {$consumerscoreAgreementMessage}
  </p>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" onclick="doNotCheckConsumerScore();" data-dismiss="modal">
    {s namespace='frontend/MoptPaymentPayone/payment' name='disagreeButtonLabel'}Nicht zustimmen{/s}
  </button>
  <button type="button" class="btn btn-primary" onclick="checkConsumerScore();">
    {s namespace='frontend/MoptPaymentPayone/payment' name='agreeButtonLabel'}Zustimmen{/s}
  </button>
</div>

<script type="text/javascript">
  <!--
  function checkConsumerScore() {
    jQuery.post( '{url controller="moptPaymentPayone" action="checkConsumerScore" forceSecure}' ,function(response) {
      if(response == 'true')
      {
        window.location = "{url controller=account action=savePayment sTarget=checkout forceSecure}";
      }
      else
      {
        window.location = "{url controller=account action=payment sTarget=checkout forceSecure}";
      }
    });
  }

  function doNotCheckConsumerScore() {
    jQuery.post( '{url controller="moptPaymentPayone" action="doNotCheckConsumerScore" forceSecure}' ,function(response) {
      if(response == 'true')
      {
        window.location = "{url controller=account action=savePayment sTarget=checkout forceSecure}";
      }
      else
      {
        window.location = "{url controller=account action=payment sTarget=checkout forceSecure}";
      }
    });
  }
  // -->
</script>