<div class="register">
  <div class="heading">
    <h2>Lieferadresse Bestätigen</h2>
  </div>
      <div style="padding: 25px;">
        <p class="none">
        <h3>Eingegebene Lieferadresse:</h3>
          {$moptShippingAddressCheckOriginalAddress.street} {$moptShippingAddressCheckOriginalAddress.streetnumber}<br>
          {$moptShippingAddressCheckOriginalAddress.zipcode}<br>
          {$moptShippingAddressCheckOriginalAddress.city}
        </p>
        <p class="none" style="margin-top: 25px;">
        <h3>Korrigierte Lieferadresse:</h3>
          {$moptShippingAddressCheckCorrectedAddress.streetname} {$moptShippingAddressCheckCorrectedAddress.streetnumber}<br>
          {$moptShippingAddressCheckCorrectedAddress.zip}<br>
          {$moptShippingAddressCheckCorrectedAddress.city}
        </p>

      <script type="text/javascript">
        <!--
        function saveOriginalAddress() {
            jQuery.post( '{url controller="moptPaymentPayone" action="saveOriginalShippingAddress"}' ,function() {
              window.location = "{url controller=account action=saveShipping sTarget=$moptShippingAddressCheckTarget}";
            });
          }
          
        function saveCorrectedAddress() {
            jQuery.post( '{url controller="moptPaymentPayone" action="saveCorrectedShippingAddress"}' ,function() {
              window.location = "{url controller=account action=saveShipping sTarget=$moptShippingAddressCheckTarget}";
            });
          }
        // -->
      </script>
      
      
  <p class="none" style="margin-top: 25px;">
    <input  class="button-middle large left" type="submit" onclick="saveCorrectedAddress();" value="Daten übernehmen"/>
    <input style="margin-bottom: 25px;" class="button-middle large left" type="submit" onclick="saveOriginalAddress();" value="Daten nicht übernehmen"/>
  </p>
      </div>
</div>