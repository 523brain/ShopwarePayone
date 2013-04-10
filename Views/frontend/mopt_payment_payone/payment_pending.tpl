{extends file='frontend/index/index.tpl'}

{block name="frontend_index_header_javascript" append}
<script type="text/javascript">
  //<![CDATA[
  if(top!=self){
    top.location=self.location;
  }
  //]]>
</script>
{/block}

{* Breadcrumb *}
{block name='frontend_index_start' append}
{$sBreadcrumb = [['name'=>"Bestellung abschließen"]]}
{/block}

{* Main content *}
{block name='frontend_index_content'}
<div id="center" class="grid_13">

  <h2>{$moptPayoneMessage|escape|nl2br}</h2>

<!--  <div class="actions">
    <a class="button-left large left" href="{url controller=checkout action=cart}" title="{s name=PaymentLinkChangeBasket}Warenkorb ändern{/s}">
      {se name=PaymentLinkChangeBasket}{/se}
    </a>
    <a class="button-right large right" href="{url controller=account action=payment sTarget=checkout sChange=1}" title="{s name=PaymentLinkChange}Zahlungsart ändern{/s}">
      {se name=PaymentLinkChange}{/se}
    </a>
  </div>-->

</div>
{/block}

{block name='frontend_index_actions'}{/block}