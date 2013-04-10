//{namespace name="backend/mopt_payment_payone/view/main"}
//{block name="backend/mopt_payment_payone/controller/main"}
Ext.define('Shopware.apps.MoptPaymentPayone.controller.Main', {
 
  /**
     * Extend from the standard ExtJS 4 controller
     * @string
     */
  extend: 'Ext.app.Controller',
 
  mainWindow: null,
 
  /**
     * Creates the necessary event listener for this
     * specific controller and opens a new Ext.window.Window
     * to display the subapplication
     *
     * @return void
     */
  init: function() {
    //    var me = this;
 
    //    me.mainWindow = me.getView('main.Window').create({
    //      listStore: me.getStore('List').load()
    //    });
    //    me.mainWindow = me.getView('main.Window').create();
    //
    //    me.callParent(arguments);
    var me = this;
 
    me.mainWindow = me.createMainWindow();
    me.callParent(arguments);
 
    return me.mainWindow;
  },
  
  /**
   * Creates and shows the list window of the favorite module.
   * @return Shopware.apps.SwagFavorites.view.list.Window
   */
  createMainWindow: function() {
    var me = this, window;
    
    window = me.getView('list.Window').create({
      listStore: Ext.create('Shopware.apps.MoptPaymentPayone.store.MoptApiLog').load()
    }).show();
    
    return window;
  }
});
//{/block}