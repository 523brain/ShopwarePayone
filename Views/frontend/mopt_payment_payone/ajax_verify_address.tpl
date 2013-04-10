<div class="register">
  <div class="heading">
    <h2>Adresse Bestätigen</h2>
  </div>
      <div style="padding: 25px;">
        <p class="none">
        <h3>Eingegebene Adresse:</h3>
          {$moptAddressCheckOriginalAddress.street} {$moptAddressCheckOriginalAddress.streetnumber}<br>
          {$moptAddressCheckOriginalAddress.zipcode}<br>
          {$moptAddressCheckOriginalAddress.city}
        </p>
        <p class="none" style="margin-top: 25px;">
        <h3>Korrigierte Adresse:</h3>
          {$moptAddressCheckCorrectedAddress.streetname} {$moptAddressCheckCorrectedAddress.streetnumber}<br>
          {$moptAddressCheckCorrectedAddress.zip}<br>
          {$moptAddressCheckCorrectedAddress.city}
        </p>

      <script type="text/javascript">
        <!--
        function saveOriginalAddress() {
            jQuery.post( '{url controller="moptPaymentPayone" action="saveOriginalAddress"}' ,function() {
              window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget}";
            });
          }
          
        function saveCorrectedAddress() {
            jQuery.post( '{url controller="moptPaymentPayone" action="saveCorrectedAddress"}' ,function() {
              window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget}";
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