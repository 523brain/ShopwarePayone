/**
 * $Id: $
 */

//{namespace name=backend/mopt_payone_transaction_forward/main}


//{block name="backend/mopt_payone_transaction_forward/controller/main"}
Ext.define('Shopware.apps.MoptPayoneTransactionForward.controller.Main', {
  /**
    * Extend from the standard ExtJS 4
    * @string
    */
  extend: 'Ext.app.Controller',

  /**
	* Creates the necessary event listener for this
	* specific controller and opens a new Ext.window.Window
	* @return void
	*/
  init: function() {
    var me = this;

    me.mainWindow = me.getView('main.Window').create({
      mappingStore: Ext.create('Shopware.apps.MoptPayoneTransactionForward.store.Mapping')
    });
  }

});
//{/block}