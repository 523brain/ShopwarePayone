//{block name="backend/order/view/detail/overview" append}
Ext.define('Shopware.apps.Order.view.detail.MoptPayoneOverview', 
{
  override: 'Shopware.apps.Order.view.detail.Overview',

  initComponent: function() {
    var me = this;
    me.callParent(arguments);
  }
});
//{/block}