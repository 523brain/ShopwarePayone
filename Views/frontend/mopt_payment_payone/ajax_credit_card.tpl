<script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>
<script src="{link file='frontend/_resources/javascript/client_api.js'}"></script>

<div class="register">
  <div class="heading">
    <h2>Kreditkarte</h2>
    {* Close button *}
    <a href="#" class="modal_close" title="Schliessen">
      {s name='LoginActionClose'}{/s}
    </a>
  </div>
  <fieldset>
    <form class="payment" action="{url controller='moptPaymentPayone' action='savePseudoCard'}" method="post" name="frmCheckCreditCard" id="frmCheckCreditCard">
      <div class="debit">
        <p class="none">
          <label for="mopt_payone__cc_accountholder_ajax">{*s name='PaymentDebitLabelName'}{/s*}Karteninhaber</label>
          <input name="mopt_payone__cc_accountholder" type="text" id="mopt_payone__cc_accountholder_ajax" class="text" />
        </p>
        <p class="none">
          <label for="mopt_payone__cc_cardtype_ajax">{*s name='PaymentDebitLabelName'}{/s*}Kartentyp</label>
          <select name="mopt_payone__cc_cardtype" id="mopt_payone__cc_cardtype_ajax" size="1" style="width:auto">
            <option value="not_choosen">Bitte auswählen...</option>
            {foreach from=$payment_mean.mopt_payone_credit_cards item=credit_card}
            <option value="{$credit_card.short}" mopt_payone__cc_paymentname="{$credit_card.name}" mopt_payone__cc_paymentid="{$credit_card.id}">{$credit_card.description}</option>
            {/foreach}
          </select>
        </p>
        <p class="none">
          <label for="mopt_payone__cc_accountnumber_ajax">{*s name='PaymentDebitLabelAccount'}{/s*}Kartennummer</label>
          <input name="mopt_payone__cc_accountnumber" type="text" id="mopt_payone__cc_accountnumber_ajax" class="text" />
        </p>
        <p class="none">
          <label for="mopt_payone__cc_month_ajax">{*s name='PaymentDebitLabelName'}{/s*}Gültig Bis</label>
          <select name="mopt_payone__cc_month" id="mopt_payone__cc_month_ajax" size="1" style="width:auto">
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
          </select>
          {html_select_date prefix='mopt_payone__cc_' end_year='+10' display_days=false display_months=false year_extra='style="width:auto" id="mopt_payone__cc_Year_ajax"'}
        </p>
        <p class="none">
          <label for="mopt_payone__cc_cvc_ajax">{*s name='PaymentDebitLabelBankcode'}{/s*}Prüfziffer</label>
          <input name="mopt_payone__cc_cvc" type="text" id="mopt_payone__cc_cvc_ajax" class="text" />
        </p>
<!--        <p class="none description">{*s name='PaymentDebitInfoFields'}{/s*}	Zusatzinfos
        </p>-->
        <input name="mopt_payone__cc_pseudocardpan" type="hidden" id="mopt_payone__cc_pseudocardpan_ajax" class="text" />
        <input name="mopt_payone__cc_status" type="hidden" id="mopt_payone__cc_status_ajax"/>
      </div>

      {*literal*}
      <script type="text/javascript">
        <!--
        
        function checkCreditCard() {        
          var data = {
            request : 'creditcardcheck',
            mode : '{$moptPayoneParams.mode}',
            mid : '{$moptPayoneParams.mid}',
            aid : '{$moptPayoneParams.aid}',
            portalid : '{$moptPayoneParams.portalid}',
            encoding : 'UTF-8',
            storecarddata : 'yes',
            hash : '{$moptPayoneParams.hash}',
            cardholder : $('#mopt_payone__cc_accountholder_ajax').val(),
            cardpan : $('#mopt_payone__cc_accountnumber_ajax').val(),
            cardtype: $('#mopt_payone__cc_cardtype_ajax').val(),
            cardexpiremonth : $('#mopt_payone__cc_month_ajax').val(),
            cardexpireyear : $('#mopt_payone__cc_Year_ajax').val(),
            cardcvc2 : $('#mopt_payone__cc_cvc_ajax').val(),
            language : '{$moptPayoneParams.language}',
            responsetype : 'JSON'
          }
          var options = {
            return_type : 'object',
            callback_function_name : 'processPayoneResponse'
          }
          var request = new PayoneRequest(data, options);
          request.checkAndStore();
        }
        
        function processPayoneResponse(response) {
          if (response.get('status') == 'VALID') {
            $('#mopt_payone__cc_accountnumber_ajax').val('');
            $('#mopt_payone__cc_cvc_ajax').val('');
            $('#mopt_payone__cc_status_ajax').val(response.get('status'));
                        
            $('#mopt_payone__cc_accountholder').val($('#mopt_payone__cc_accountholder_ajax').val());
            $('#mopt_payone__cc_paymentdescription').val($('#mopt_payone__cc_cardtype_ajax option:selected').text());
            $('#mopt_payone__cc_truncatedcardpan').val(response.get('truncatedcardpan'));
            $('#mopt_payone__cc_month').val($('#mopt_payone__cc_month_ajax').val());
            $('#mopt_payone__cc_year').val($('#mopt_payone__cc_Year_ajax').val());
            $('#mopt_payone__cc_pseudocardpan').val(response.get('pseudocardpan'));
            $('#mopt_payone__cc_paymentid').val($('#mopt_payone__cc_cardtype_ajax option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_cardtype').val($('#mopt_payone__cc_cardtype_ajax').val());
            $('#mopt_payone__cc_paymentname').val($('#mopt_payone__cc_cardtype_ajax option:selected').attr('mopt_payone__cc_paymentname'));
            $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_cardtype_ajax option:selected').attr('mopt_payone__cc_paymentid'));
    
            var data = {
              mopt_payone__cc_accountholder : $('#mopt_payone__cc_accountholder_ajax').val(),
              mopt_payone__cc_truncatedcardpan : response.get('truncatedcardpan'),
              mopt_payone__cc_cardtype: $('#mopt_payone__cc_cardtype_ajax').val(),
              mopt_payone__cc_month : $('#mopt_payone__cc_month_ajax').val(),
              mopt_payone__cc_year : $('#mopt_payone__cc_Year_ajax').val(),
              mopt_payone__cc_pseudocardpan : response.get('pseudocardpan'),
              mopt_payone__cc_paymentname : $('#mopt_payone__cc_cardtype_ajax option:selected').attr('mopt_payone__cc_paymentname'),
              mopt_payone__cc_paymentid : $('#mopt_payone__cc_cardtype_ajax option:selected').attr('mopt_payone__cc_paymentid'),
              mopt_payone__cc_paymentdescription : $('#mopt_payone__cc_cardtype_ajax option:selected').text(),
              mopt_payone__target : $('#mopt_payone__target').val()
            }
            
            var target;
            if(!$('#mopt_payone__target').val())
            {
              target = 'checkout';
            }
            else
            {
              target = $('#mopt_payone__target').val();
            }
            jQuery.post( '{url controller="moptPaymentPayone" action="savePseudoCard"}' , data ,function() {
              window.location = "{url controller=account action=savePayment sTarget=$sTarget}" + target;
            });
          }
          else {
            $('#mopt_payone__cc_cvc_ajax').val('');
            alert(response.get('customermessage'));
          }
        }
        // -->
      </script>
      {*/literal*}
    </form>
  </fieldset>
  <p class="none">
    <input class="button-right large left" type="submit" onclick="checkCreditCard();"/>
  </p>
</div>