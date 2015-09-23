{extends file="frontend/account/payment.tpl"}

{block name="frontend_index_header_javascript_jquery" append}
    {if $moptConsumerScoreCheckNeedsUserAgreement}
        <script type="text/javascript">
            $(document).ready(function () {
                $.post('{url controller=moptAjaxPayone action=ajaxGetConsumerScoreUserAgreement forceSecure}', function (data) {
                    $('.content-main').prepend(data);
                });
            });
        </script>
    {/if}
    <script src="{link file='frontend/_resources/javascript/client_api.js'}"></script>
    <script src="{link file='frontend/_resources/javascript/mopt_payment.js'}"></script>
{/block}
