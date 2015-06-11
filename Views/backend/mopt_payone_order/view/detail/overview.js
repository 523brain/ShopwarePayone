//{namespace name=backend/mopt_payone_order/main}
//{block name="backend/order/view/detail/overview" append}
Ext.define('Shopware.apps.Order.view.detail.MoptPayoneOverview', 
{
  override: 'Shopware.apps.Order.view.detail.Overview',

  initComponent: function() 
  {
    var me = this;
    me.callParent(arguments);
    
    if(/mopt_payone__/.test(me.record.raw.payment.name))
    {
      me.items.insert(2, me.createMoptPayoneShippingCostContainer());
    }
    else
    {
      me.items.insert(2, me.createMoptPayoneNoPayoneOrderContainer());
    }
  },
  
  /**
   * Creates the Ext.panel.Panel for the PAYONE shipping costs status
   */
  createMoptPayoneShippingCostContainer: function() 
  {
      var me = this;
      var moptShowShippingCosts = true;

      for (var i = 0; i < me.record.raw.details.length; i++)
      {
        if (me.record.raw.details[i].articleNumber === "SHIPPING")
        {
          moptShowShippingCosts = false;
        }
      }

      if(moptShowShippingCosts)
      {
        return Ext.create('Ext.panel.Panel', {
          title: '{s name=overview/title}PAYONE: Versandkosten{/s}',
          bodyPadding: 10,
          flex: 1,
          paddingRight: 5,
          margin: '0 0 10 0',
          height: 100,
          items: [
              {
                  xtype: 'container',
                  renderTpl: me.createMoptPayoneShippingCostTemplate(),
                  renderData: me.record.raw.attribute
              }
          ]
        });
      }
      else
      {
        return Ext.create('Ext.panel.Panel', {
          title: '{s name=overview/title}PAYONE: Versandkosten{/s}',
          bodyPadding: 10,
          flex: 1,
          paddingRight: 5,
          margin: '0 0 10 0',
          height: 100,
          items: [
              {
                  xtype: 'container',
                  renderTpl: me.createMoptPayoneShippingCostTemplateExtraPosition()
              }
          ]
        });
      }
  },
  
  /**
   * Creates the Ext.panel.Panel for the PAYONE shipping costs status
   */
  createMoptPayoneNoPayoneOrderContainer: function() 
  {
    var me = this;
    
    return Ext.create('Ext.panel.Panel', {
      title: '{s name=overview/title}PAYONE: Versandkosten{/s}',
      bodyPadding: 10,
      flex: 1,
      paddingRight: 5,
      margin: '0 0 10 0',
      height: 100,
      items: [
          {
              xtype: 'container',
              renderTpl: me.createMoptPayoneNoPayoneOrderTemplate()
          }
      ]
    });
  },

  /**
   * Creates the XTemplate for the ShippingCost information panel
   *
   * @return [Ext.XTemplate] generated Ext.XTemplate
   */
  createMoptPayoneShippingCostTemplate:function () 
  {
      var labelCaptured = '{s name=overview/captured}Bisher eingezogenen: {/s}';
      var labelDebited = '{s name=overview/debited}Bisher gutgeschrieben: {/s}';
      
      return new Ext.XTemplate(
          '{literal}<tpl for=".">',
              '<div class="customer-info-pnl">',
                  '<div class="base-info">',
                      '<p>',
                          '<span>' + labelCaptured + '{moptPayoneShipCaptured}</span>',
                      '</p>',
                      '<p>',
                          '<span>' + labelDebited + '{moptPayoneShipDebit}</span>',
                      '</p>',
                  '</div>',
              '</div>',
          '</tpl>{/literal}'
      );
  },

  /**
   * Creates the XTemplate for the ShippingCost information panel
   *
   * @return [Ext.XTemplate] generated Ext.XTemplate
   */
  createMoptPayoneShippingCostTemplateExtraPosition:function () 
  {
      var labelExtraPositon = '{s name=overview/extraPosition}Die Versandkosten sind als eigener Artikel in der Positionsliste verfügbar.{/s}';
      
      return new Ext.XTemplate(
          '{literal}<tpl for=".">',
              '<div class="customer-info-pnl">',
                  '<div class="base-info">',
                      '<p>',
                          '<span>' + labelExtraPositon + '</span>',
                      '</p>',
                  '</div>',
              '</div>',
          '</tpl>{/literal}'
      );
  },
  
  /**
   * Creates the XTemplate for the ShippingCost information panel
   *
   * @return [Ext.XTemplate] generated Ext.XTemplate
   */
  createMoptPayoneNoPayoneOrderTemplate:function () 
  {
      var labelNotPayone = '{s name=overview/notPayone}Diese Bestellung wurde nicht mit einer PAYONE Zahlart durchgeführt.{/s}';
      
      return new Ext.XTemplate(
          '{literal}<tpl for=".">',
              '<div class="customer-info-pnl">',
                  '<div class="base-info">',
                      '<p>',
                          '<span>' + labelNotPayone + '</span>',
                      '</p>',
                  '</div>',
              '</div>',
          '</tpl>{/literal}'
      );
  }
  
});
//{/block}