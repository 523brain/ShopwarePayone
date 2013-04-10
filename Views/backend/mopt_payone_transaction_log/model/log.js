/**
 * $Id: $
 */

/**
 * Shopware - Log model
 *
 * This model represents a single log of s_core_log.
 */
//{block name="backend/mopt_payone_transaction_log/model/log"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.model.Log', {
  /**
    * Extends the standard ExtJS 4
    * @string
    */
  extend: 'Ext.data.Model',
  /**
    * The fields used for this model
    * @array
    */
  fields: [
  //{block name="backend/mopt_payone_transaction_log/model/log/fields"}{/block}
  'id',
  'transactionId',
  'orderNr',
  'status',
  {
    name: 'transactionDate',
    type: 'date',
    dateFormat:'Y-m-d'
  },
  'sequenceNr',
  'paymentId',
  'liveMode',
  'portalId',
  'claim',
  'balance',
  {
    name: 'creationDate',
    type: 'date',
    dateFormat:'Y-m-d'
  },
  {
    name: 'updateDate',
    type: 'date',
    dateFormat:'Y-m-d'
  }
  ]
  ,
  /**
    * Configure the data communication
    * @object
    */
  proxy: {
    type: 'ajax',
    api: {
      read: '{url controller="MoptPayoneTransactionLog" action="getTransactionLogs"}',
      detail: '{url controller="MoptApilogPayone" action="getDetailLog"}'
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