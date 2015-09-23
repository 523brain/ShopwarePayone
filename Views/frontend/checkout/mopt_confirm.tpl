{extends file="frontend/checkout/confirm.tpl"}

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

{* Payment selection *}

{block name="frontend_index_header_javascript" append}
    {if $moptAgbChecked}
        <script type="text/javascript">
            $(document).ready(function () {
                $('#sAGB').prop('checked', true);
                $('#sAGB').attr('checked', 'checked');
                $('input[name=sAGB]').val(1);
            });
        </script>
    {/if}
    {if $moptMandateData.mopt_payone__showMandateText}
        <script type="text/javascript">
            $(document).ready(function () {
                $('#mandate_status').bind('change', function (e) {
                    if ($('#mandate_status').is(':checked'))
                    {
                        $('#moptMandateConfirm').prop('checked', true);
                        $('#moptMandateConfirm').attr('checked', 'checked');
                        $('input[name=moptMandateConfirm]').val(1);
                    }
                    else {
                        $('#moptMandateConfirm').prop('checked', false);
                        $('#moptMandateConfirm').attr('checked', false);
                        $('input[name=moptMandateConfirm]').val(0);
                    }
                });
            });
        </script>
    {/if}
{/block}

{block name="frontend_checkout_confirm_confirm_table_actions" prepend}
    {if $moptMandateData.mopt_payone__showMandateText}
        <div>
            <div style="overflow:scroll; border:1px solid #ccc; padding:10px; height:200px;">
                {$moptMandateData.mopt_payone__mandateText}
            </div>
            <div class="clear">&nbsp;</div>
            <div> 
                <label for="mandate_status"  style="float:left; padding-right:10px;">
                    {s name='mandateIAgree' namespace='frontend/MoptPaymentPayone/payment'}Ich möchte das Mandat erteilen{/s}
                    <br />
                    {s name='mandateElectronicSubmission' namespace='frontend/MoptPaymentPayone/payment'}(elektronische Übermittlung){/s}
                </label>
                <input type="checkbox" id="mandate_status" name="mandate_status"/>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
    {/if}
{/block}

{block name="frontend_checkout_confirm_agb_checkbox" append}
    {if $moptMandateData.mopt_payone__showMandateText}
        <input name="moptMandateConfirm" type="hidden" 
               id="moptMandateConfirm" type="checkbox"/>
    {/if}
{/block}

{block name="frontend_checkout_confirm_error_messages" prepend}
    {if $moptMandateAgreementError}
        {include file="frontend/_includes/messages.tpl" type="error" content="{s name='mandateAgreementError' namespace='frontend/MoptPaymentPayone/payment'}Bitte bestätigen Sie die Erteilung des Mandats.{/s}"}
    {/if}
{/block}
