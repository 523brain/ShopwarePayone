{extends file="frontend/account/shipping.tpl"}

{block name="frontend_index_header_javascript" append}
    {if $moptShippingAddressCheckNeedsUserVerification}
        <script type="text/javascript">
            $(document).ready(function () {
                $.post('{url controller=moptAjaxPayone action=ajaxVerifyShippingAddress forceSecure}', function (data) {
                    $('.content-main').prepend(data);
                });
            });
        </script>
    {/if}
{/block}