/**
 * $Id: $
 */
 
//{block name="backend/mopt_payone_transaction_forward/application"}
Ext.define('Shopware.apps.MoptPayoneTransactionForward', {
  name:'Shopware.apps.MoptPayoneTransactionForward',
  extend:'Enlight.app.SubApplication',
  bulkLoad: true,
  loadPath:'{url action=load}',

  controllers: [ 'Main' ],
  stores: [ 'Mapping' ],
  models: [ 'Mapping' ],
  views: [ 'main.Window', 'mapping.List' ],
//  views: [ 'main.Window' ],

  launch: function() {
    var me = this,
    mainController = me.getController('Main');

    return mainController.mainWindow;
  }
});
//{/block}