/**
 * $Id: $
 */

/**
 * model for payone transaction log
 */
//{block name="backend/mopt_payment_payone/model/mopt_transaction_log"}
Ext.define('Shopware.apps.MoptPaymentPayone.model.MoptTransactionLog', {
  extend: 'Ext.data.Model',
  fields: [
  {
    name: 'id', 
    type: 'int'
  },
  {
    name: 'transactionId',  
    type: 'int'
  },
  {
    name: 'orderNr', 
    type: 'int'
  },
		
  {
    name: 'status', 
    type: 'string'
  },
  {
    name: 'transactionDate', 
    type: 'date', 
    dateFormat: 'c'
  },
  {
    name: 'sequenceNr', 
    type: 'int'
  },
  {
    name: 'paymentId', 
    type: 'int'
  },
		
  {
    name: 'liveMode', 
    type: 'bool'
  },
  {
    name: 'portalId', 
    type: 'int'
  },
  {
    name: 'claim', 
    type: 'float'
  },
  {
    name: 'balance', 
    type: 'float'
  },
  {
    name: 'creationDate', 
    type: 'date', 
    dateFormat: 'c'
  },

  {
    name: '$updateDate', 
    type: 'date', 
    dateFormat: 'c'
  },
  ]
});
//{/block}