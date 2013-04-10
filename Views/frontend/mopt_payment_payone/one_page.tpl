{extends file='frontend/index/index.tpl'}

{block name='frontend_index_content_left'}{/block}

{* Breadcrumb *}
{block name='frontend_index_start' append}
{$sBreadcrumb = [['name'=>"{s name=PaymentTitle}mo test Breadcrumb erweitert Zahlung durchführen{/s}"]]}
{/block}

{* Main content *}
{block name="frontend_index_content"}
<div id="payment" class="grid_20" style="margin:10px 0 10px 20px;width:959px;">

  <h2 class="headingbox_dark largesize">{se name="PaymentHeader"}Bitte führen Sie nun die Zahlung durch:{/se}</h2>
  {*<div id="payment_loader" class="ajaxSlider" style="height:100px;border:0 none;">
    <div class="loader" style="width:80px;margin-left:-50px;">{s name="PaymentInfoWait"}Bitte warten...{/s}</div>
  </div>
  <div> *}
    <ul>
      <li>status: {$adresscheckResponseStatus}</li>
      {*
      <li>secstatus: {$adresscheckResponse.secstatus}</li>
      <li>personstatus: {$adresscheckResponse.personstatus}</li>
      <li>street: {$adresscheckResponse.street}</li>
      <li>streetname: {$adresscheckResponse.streetname}</li>
      <li>streetnumber: {$adresscheckResponse.streetnumber}</li>
      <li>zip: {$adresscheckResponse.zip}</li>
      *}
    </ul>
  </div>
  <div>
    <form method="POST" action="{$gatewayUrl}"><br>
      <input type="submit" value="Bestellung abschliessen">
    </form>
  </div>
</div>
<div class="doublespace">&nbsp;</div>
{/block}