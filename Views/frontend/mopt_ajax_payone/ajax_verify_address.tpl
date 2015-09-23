<div class="js--modal sizing--content" style="width: 600px; height: auto; display: block; opacity: 1;">
    <div class="header">
        <div class="title">
            {s namespace='frontend/MoptPaymentPayone/payment' name='confirmAddressTitle'}Adresse Bestätigen{/s}
        </div>
    </div>
    <div class="content" style="padding: 25px;">
        <p class="none">
        <h3>
            {s namespace='frontend/MoptPaymentPayone/payment' name='originalAddressTitle'}Eingegebene Adresse:{/s}
        </h3>
        {$moptAddressCheckOriginalAddress.street}<br>
        {$moptAddressCheckOriginalAddress.zipcode}<br>
        {$moptAddressCheckOriginalAddress.city}
        </p>
        <p class="none" style="margin-top: 25px;">
        <h3>
            {s namespace='frontend/MoptPaymentPayone/payment' name='correctedAddressTitle'}Korrigierte Adresse:{/s}
        </h3>
        {$moptAddressCheckCorrectedAddress.street}<br>
        {$moptAddressCheckCorrectedAddress.zip}<br>
        {$moptAddressCheckCorrectedAddress.city}
        </p>

        <p class="none" style="margin-top: 25px;">
            <input class="btn" type="submit" onclick="saveCorrectedAddress();" 
                   value="{s namespace='frontend/MoptPaymentPayone/payment' name='saveDataButtonLabel'}Daten übernehmen{/s}"/>
            <input style="margin-bottom: 25px;" class="btn" type="submit" onclick="saveOriginalAddress();" 
                   value="{s namespace='frontend/MoptPaymentPayone/payment' name='dontSaveDataButtonLabel'}Daten nicht übernehmen{/s}"/>
        </p>
    </div>
</div>
<div class="js--overlay is--open" style="opacity: 0.8; display: block"></div>
{block name="frontend_index_header_javascript" append}
    <script type="text/javascript">
        //<!--
      function saveOriginalAddress()
        {
            jQuery.post('{url controller="moptAjaxPayone" action="saveOriginalAddress" forceSecure}', function ()
            {
                window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget forceSecure}";
            });
        }

        function saveCorrectedAddress()
        {
            jQuery.post('{url controller="moptAjaxPayone" action="saveCorrectedAddress" forceSecure}', function ()
            {
                window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget forceSecure}";
            });
        }
        // -->
    </script>
{/block}
