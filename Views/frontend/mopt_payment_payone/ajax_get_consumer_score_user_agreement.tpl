<div>
  <div class="heading">
    <h2>Bonitätsprüfung Bestätigen</h2>
  </div>
  <div style="padding: 25px;">
    <p style="padding: 0 25px 0 0;" class="none">
      {$consumerscoreNoteMessage}
    </p>
    <p style="padding: 25px 0 25px 0;" class="none">
      {$consumerscoreAgreementMessage}
    </p>

    <script type="text/javascript">
      <!--
      function checkConsumerScore() {
        jQuery.post( '{url controller="moptPaymentPayone" action="checkConsumerScore"}' ,function(response) {
          if(response == 'true')
          {
            window.location = "{url controller=account action=savePayment sTarget=checkout}";
          }
          else
          {
            window.location = "{url controller=account action=payment sTarget=checkout}";
          }
        });
      }
          
      function doNotCheckConsumerScore() {
        jQuery.post( '{url controller="moptPaymentPayone" action="doNotCheckConsumerScore"}' ,function(response) {
          if(response == 'true')
          {
            window.location = "{url controller=account action=savePayment sTarget=checkout}";
          }
          else
          {
            window.location = "{url controller=account action=payment sTarget=checkout}";
          }
        });
      }
      // -->
    </script>


    <input style="margin-left: 5px;" class="button-middle large right" type="submit" onclick="checkConsumerScore();" value="Zustimmen"/>
    <input style="margin-bottom: 25px;" class="button-middle large right" type="submit" onclick="doNotCheckConsumerScore();" value="Nicht zustimmen"/>
  </div>
</div>