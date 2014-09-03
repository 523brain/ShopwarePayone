//{block name="backend/order/view/detail/position" append}
Ext.define('Shopware.apps.Order.view.detail.MoptPayonePosition', {
  override: 'Shopware.apps.Order.view.detail.Position',
  
  createGridToolbar: function() {
    var me = this;
    var toolbar = me.callParent(arguments);
    
    me.moptPayoneCapturePositionsButton = Ext.create('Ext.button.Button', {
      iconCls: 'sprite-money-coin',
      text: 'Positionen einziehen',
      action: 'moptPayoneCapturePositions',
      handler: function() {
        me.fireEvent('moptPayoneCapturePositions', me.record, me.orderPositionGrid, {
            callback: function(order) {
                me.fireEvent('updateForms', order, me.up('window'));
            }
        });
      }
    });
    
    me.moptPayoneDebitPositionsButton = Ext.create('Ext.button.Button', {
      iconCls: 'sprite-money-coin',
      text: 'Positionen gutschreiben',
      action: 'moptPayoneDebitPositions',
      handler: function() {
        me.fireEvent('moptPayoneDebitPositions', me.record, me.orderPositionGrid, {
            callback: function(order) {
                me.fireEvent('updateForms', order, me.up('window'));
            }
        });
      }
    });
    
    toolbar.items.add(me.moptPayoneCapturePositionsButton);
    toolbar.items.add(me.moptPayoneDebitPositionsButton);
    
    return toolbar;
  },
  
  registerEvents: function() {
    var me = this;
    me.callParent(arguments);
    
    this.addEvents('moptPayoneCapturePositions', 'moptPayoneDebitPositions');
  },
          
  getColumns:function (grid) {
    var me = this;
    columns = me.callParent(arguments);
    
    columns.push({
        header: 'Eingezogen',
        dataIndex: 'moptPayoneCaptured',
        flex:1
      },
      {
        header: 'Gutgeschrieben',
        dataIndex: 'moptPayoneDebit',
        flex:1
      }
    );
    return columns;
  }
          
  
});
//{/block}