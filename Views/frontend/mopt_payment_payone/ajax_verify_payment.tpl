<div class="register">
  <div class="heading">
    <h2>Zahlart Bestätigen</h2>
  </div>
  <div style="padding: 0px 0px 0px 23px;">
    Sie haben die Zahlart geändert</br>
    Neue Zahlart: <span style="font-weight: bold;">{$moptSelectedPayment}</span></br>
    Bisher gewählte Zahlart: <span style="font-weight: bold;">{$moptOriginalPayment}</span></br>

    <script type="text/javascript">
      <!--
      function savePayment() {
        if('{$moptCheckedId}' == 'payment_meanmopt_payone_creditcard')
        {
          checkCreditCard();
          return false;
        }
        else
        {
        
          //submit payment form
          var forms = $('.payment').get();
      
          $.each(forms, function() {
            var me = this;
            var action = me.getAttribute('action');
            if (action.indexOf("savePayment") >= 0)
            {
              me.submit();  
            }
          });
        }
      }
          
      function reloadPage() {
        window.location = "{url controller=checkout action=confirm}";
      }
      // -->
    </script>


    <p class="none" style="margin-top: 25px;">
      <input  class="button-middle large left" type="submit" onclick="savePayment();" value="Neue übernehmen"/>
      <input style="margin-bottom: 25px;" class="button-middle large left" type="submit" onclick="reloadPage();" value="Nicht übernehmen"/>
    </p>
  </div>
</div>