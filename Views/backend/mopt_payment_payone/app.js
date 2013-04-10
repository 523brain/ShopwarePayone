//{block name="backend/mopt_payment_payone/application"}
Ext.define('Shopware.apps.MoptPaymentPayone', {
   
  /**
     * The name of the module. Used for internal purpose
     * @string
     */
  name:'Shopware.apps.MoptPaymentPayone',
  
  /**
     * Extends from our special controller, which handles the
     * sub-application behavior and the event bus
     * @string
     */
  extend:'Enlight.app.SubApplication',

 
  /**
     * Enable bulk loading
     * @boolean
     */
  bulkLoad:true,
  
  /**
     * Sets the loading path for the sub-application.
     *
     * @string
     */
  loadPath:'{url action="load"}',
  
   /**
     * Required views for this sub-application
     * @array
     */
  //    views: [ 'main.Window', 'list.List' ],
  views: [ 'main.Window', 'main.MoptApiLog'],
 
  /**
     * Required stores for sub-application
     * @array
     */
  stores: [ 'MoptApiLog'],
  
  /**
     * Requires models for sub-application
     * @array
     */
  models: ['MoptApiLog'],
 
  /**
     * Required controllers for sub-application
     * @array
     */
  //    controllers: ['Main', 'List'],
  controllers: ['Main'],

 

 
  /**
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
  launch: function() {
    var me = this,
    mainController = me.getController('Main');
 
    return mainController.mainWindow;
  }
});
//{/block}