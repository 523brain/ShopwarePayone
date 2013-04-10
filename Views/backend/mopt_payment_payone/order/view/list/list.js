//{block name="backend/order/view/list/list" append}
Ext.override(Shopware.apps.Order.view.list.List, {
  getColumns: function() {
    var me = this,
    columns = me.callParent(arguments);

    columns.push(            {
      header: 'PayOne Transaction ID',
      dataIndex: 'moptPayoneTxid',
      flex:1,
      renderer: me.shopColumn
    },            {
      header: 'PayOne Payment Status',
      dataIndex: 'moptPayoneStatus',
      flex:1,
      renderer: me.shopColumn
    });

    return columns;
  }
});
//{/block}

