/* 
 * $Id: $
 */

/**
 * api log view
 */
Ext.define('Shopware.apps.MoptPaymentPayone.view.MoptApiLog', {

  extend: 'Ext.grid.Panel',
  alias:'widget.mopt-api-log-list',

  title: 'API Log',
  anchor: '100%',
  height: 200,
  margin: '5 0',
  viewConfig: {
    stripeRows: true
  },
  selType: 'cellmodel',
    
  columns: [{
    text     : 'Id',
    width    : 85,
    dataIndex: 'id',
    field    : 'textfield'
  },{
    text     : 'request',
    width    : 160,
    dataIndex: 'request',
    field    : 'textfield'
  },{
    text     : 'response',
    width    : 60,
    dataIndex: 'response',
    field    : 'textfield'
  },{
    text     : 'live mode',
    width    : 60,
    dataIndex: 'quantity',
    field    : 'checkbox'
  },{
    text     : 'merchant id',
    width    : 60,
    dataIndex: 'merchantId',
    field    : 'numberfield'
  },{
    text     : 'portal id',
    width    : 60,
    align    : 'right',
    dataIndex: 'portalId',
    field    : 'numberfield'
  },{
    text     : 'creation date',
    width    : 80,
    sortable : true,
    align    : 'right',
    dataIndex: 'creationDate',
    field    : 'datefield'
  }],
  initComponent: function() {
            var me = this;
//        me.registerEvents();
    //        me.columns = me.createColumns();
    //        me.tbar = me.createToolBar();
        me.callParent(arguments);
  }
});


