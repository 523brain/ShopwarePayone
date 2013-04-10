//{block name="backend/payment/view/main/window" append}
Ext.override(Shopware.apps.Payment.view.main.Window, {
  /**
     * This function creates the tabPanel with its items
     * @return [Ext.tab.Panel]
     */
  createTabPanel: function() {

    var me = this, result;
 
    result = me.callParent(arguments);
    result.add(me.createPayoneTab());
 
    return result;
  },
  
  /**
     * @return Ext.container.Container
     */
  createPayoneTab: function() {
    var me = this;
 
    me.payoneTab = Ext.create('Ext.container.Container', {
      title: 'Test',
      layout: 'fit',
      record: me.record,
      paymentStore: me.paymentStore,
      items: [ {
        xtype: 'payment-payone-formpanel'
      } ]
    });
 
    return me.payoneTab;
  }
});
//{/block}


