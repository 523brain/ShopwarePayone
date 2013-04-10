/**
 * api log store
 */
//{block name="backend/mopt_payment_payone/store/mopt_api_log"}
Ext.define('Shopware.apps.MoptPaymentPayone.store.MoptApiLog', {
  /**
     * Define that this component is an extension of the Ext.data.Store
     */
  extend: 'Ext.data.Store',
  /**
     * Define how much rows loaded with one request
     */
//  pageSize: 30,
  /**
     * Auto load the store after the component
     * is initialized
     * @boolean
     */
//  autoLoad: false,
  /**
     * Enable remote sorting
     */
  remoteSort: false,
 
  /**
     * Enable remote filtering
     */
  remoteFilter: false,
  /**
     * Define the used model for this store
     * @string
     */
  model: 'MoptPaymentPayone.model.MoptApiLog',
  /**
     * Configure the data communication
     * @object
     */
  proxy: {
    type: 'ajax',
    url : '{url controller="MoptPaymentPayone" action="apiLogs"}',
    
    /**
         * Configure the data reader
         * @object
         */
    reader:{
      type:'json',
      root:'data',
      totalProperty:'total'
    }
  }
  
});
//{/block}