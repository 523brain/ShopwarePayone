/**
 * $Id: $
 */

/**
 * model for payone api log
 */
//{block name="backend/mopt_payment_payone/model/mopt_api_log"}
Ext.define('Shopware.apps.MoptPaymentPayone.model.MoptApiLog', {
  extend: 'Ext.data.Model',
  fields: [
  {
    name: 'id', 
    type: 'int'
  },
  {
    name: 'request',  
    type: 'string'
  },
  {
    name: 'response', 
    type: 'string'
  },
  {
    name: 'liveMode', 
    type: 'bool'
  },
  {
    name: 'merchantId', 
    type: 'int'
  },
  {
    name: 'portalId', 
    type: 'int'
  }]
});
//{/block}