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
{$sBreadcrumb = [['name'=>"{s name=PaymentTitle}Zahlung durchführen{/s}"]]}
{/block}

{* Main content *}
{block name='frontend_index_content'}
<div id="center" class="grid_13">

  <h2>{$errormessage|escape|nl2br}</h2>
  <br />
  <h3>{se name=PaymentErrorInfo}Bitte kontaktieren Sie den Shopbetreiber.{/se}</h3>
  <br />
  <h3>{se name=PaymentFailInfo}Bitte versuchen Sie es mit einer anderen Zahlungsart nochmal.{/se}</h3>

  <br />

  <div class="actions">
    <a class="button-left large left" href="{url controller=checkout action=cart forceSecure}" title="{s name=PaymentLinkChangeBasket}Warenkorb ändern{/s}">
      {se name=PaymentLinkChangeBasket}{/se}
    </a>
    <a class="button-right large right" href="{url controller=account action=payment sTarget=checkout sChange=1 forceSecure}" title="{s name=PaymentLinkChange}Zahlungsart ändern{/s}">
      {se name=PaymentLinkChange}{/se}
    </a>
  </div>

</div>
{/block}

{block name='frontend_index_actions'}{/block}
{*block name='frontend_index_checkout_actions'}{/block*}