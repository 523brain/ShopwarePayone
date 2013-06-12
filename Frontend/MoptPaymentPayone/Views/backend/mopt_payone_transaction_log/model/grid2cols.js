/**
 * $Id: $
 */

/**
 * Shopware - Log model
 *
 * This model represents a single log of s_core_log.
 */
//{block name="backend/mopt_payone_transaction_log/model/grid2cols"}
Ext.define('Shopware.apps.MoptPayoneTransactionLog.model.Grid2cols', {
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
  //{block name="backend/mopt_payone_transaction_log/model/grid2cols/fields"}{/block}
  'name',
  'value'
  ]
  
});
//{/block}