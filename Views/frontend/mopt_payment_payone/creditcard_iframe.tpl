{extends file='frontend/checkout/payment.tpl'}

{block name="frontend_index_content"}

<div id="payment" class="grid_20" style="margin:10px 0 10px 20px;width:959px;height:900px;">
  <h2 class="headingbox_dark largesize">{se name="PaymentHeader" namespace="frontend/checkout/payment"}Bitte führen Sie nun die Zahlung durch:{/se}</h2>
	<iframe src="{$gatewayUrl}"
		scrolling="yes"
		style="x-overflow: none; height:900px;"
		width="100%" height="100%" frameborder="0" border="0"
    name="payoneiframe" id="payoneiframe">
	</iframe>
</div>
  
<div class="doublespace">&nbsp;</div>

<div class="actions">
    <a class="btn" href="{url controller=checkout action=confirm}">
      {se name="MoptPayoneBackToConfirmPage"}Zurück zu Prüfen und Bestellen{/se}
    </a>
  </div>

{/block}