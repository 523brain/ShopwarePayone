/**
 * $Id: $
 */

/**
 * Shopware - Logs store
 *
 * This store contains all logs.
 */
//{block name="backend/mopt_payone_transaction_forward/store/mapping"}
Ext.define('Shopware.apps.MoptPayoneTransactionForward.store.Mapping', {
  /**
   * Extend for the standard ExtJS 4
   * @string
   */
  extend: 'Ext.data.Store',
  /**
   * Auto load the store after the component
   * is initialized
   * @boolean
   */
  autoLoad: false,
  /**
   * Amount of data loaded at once
   * @integer
   */
  pageSize: 20,
//  remoteFilter: true,
//  remoteSort: true,
  /**
   * Define the used model for this store
   * @string
   */
  model: 'Shopware.apps.MoptPayoneTransactionForward.model.Mapping',
  // Default sorting for the store
//  sortOnLoad: true,
  /**
   * Configure the data communication
   * @object
   */
  proxy: {
    type: 'ajax',
    api: {
      read: '{url controller="MoptPayoneTransactionForward" action="getTransactionForward"}',
      remove: '{url controller="MoptPayoneTransactionForward" action="getTransactionForward"}',
      save: '{url controller="MoptPayoneTransactionForward" action="getTransactionForward"}'
    },
    /**
     * Configure the data reader
     * @object
     */
    reader: {
      type: 'json',
      root: 'data',
      //total values, used for paging
      totalProperty: 'total'
    }
  }
});
//{/block}