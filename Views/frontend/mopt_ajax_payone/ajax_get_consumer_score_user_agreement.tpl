<div class="js--modal sizing--content" style="width: 600px; height: auto; display: block; opacity: 1;">
    <div class="header">
        <div class="title">
            {s namespace='frontend/MoptPaymentPayone/payment' name='confirmConsumerScoreCheckTitle'}Bonitätsprüfung Bestätigen{/s}
        </div>
    </div>
    <div class="content" style="padding: 25px;">
        {if isset($consumerscoreNoteMessage)}
            <p style="padding: 0 25px 0 0;" class="none">
                {$consumerscoreNoteMessage}
            </p>
        {/if}
        {if isset($consumerscoreAgreementMessage)}
            <p style="padding: 25px 0 25px 0;" class="none">
                {$consumerscoreAgreementMessage}
            </p>
        {/if}
        <input style="margin-left: 5px;" class="btn" type="submit" onclick="checkConsumerScore();" 
               value="{s namespace='frontend/MoptPaymentPayone/payment' name='agreeButtonLabel'}Zustimmen{/s}"/>
        <input style="margin-bottom: 25px;" class="btn" type="submit" onclick="doNotCheckConsumerScore();" 
               value="{s namespace='frontend/MoptPaymentPayone/payment' name='disagreeButtonLabel'}Nicht zustimmen{/s}"/>
    </div>
</div>
<div class="js--overlay is--open" style="opacity: 0.8; display: block"></div>
{block name="frontend_index_header_javascript" append}
    <script type="text/javascript">
        <!--
      function checkConsumerScore() {
            jQuery.post('{url controller="moptAjaxPayone" action="checkConsumerScore" forceSecure}', function (response)
            {
                if (response === 'true')
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
            jQuery.post('{url controller="moptAjaxPayone" action="doNotCheckConsumerScore" forceSecure}', function (response)
            {
                if (response === 'true')
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
{/block}