{extends file="frontend/account/billing.tpl"}

{block name="frontend_index_header_javascript" append}
    {if $moptAddressCheckNeedsUserVerification}
        <script type="text/javascript">
            $(document).ready(function () {
                $.post('{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}', function (data) {
                    $('.content-main').prepend(data);
                });
            });
        </script>
    {/if}
{/block}
