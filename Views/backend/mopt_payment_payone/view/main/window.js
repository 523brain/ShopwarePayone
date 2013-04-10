//{namespace name=backend/mopt_payment_payone/main}
//{block name="backend/mopt_payment_payone/view/main/window"}
Ext.define('Shopware.apps.MoptPaymentPayone.view.main.Window', {
  extend: 'Enlight.app.Window',
  title: 'PayOne Overview',
  alias: 'widget.mopt-payment-payone-main-window',
  border: false,
  autoShow: true,
  layout: 'border',
  height: 650,
  width: 925,
 
  /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
  //  initComponent: function() {
  //    var me = this;
  //    me.items = [{
  //      xtype: 'customer_basket-list-list',
  //      listStore: me.listStore
  //    }];
  // 
  //    me.callParent(arguments);
  //  }
  initComponent: function() {
    var me = this;
    
     me.items = [{
            xtype: 'mopt-api-log-list',
            store: me.listStore
        }];
    
    me.callParent(arguments);
  }
});
//{/block}