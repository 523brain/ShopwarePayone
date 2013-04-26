/**
 * $Id: $
 */

//{block name="backend/mopt_payone_transaction_forward/model/mapping"}
Ext.define('Shopware.apps.MoptPayoneTransactionForward.model.Mapping', {
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
    //{block name="backend/mopt_payone_transaction_forward/model/mapping/fields"}{/block}
    'id',
    'status',
    'urls',

  ]
});
//{/block}