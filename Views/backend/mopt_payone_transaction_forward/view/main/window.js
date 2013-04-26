/**
 * $Id: $
 */

//{namespace name=backend/mopt_payone_transaction_log/main}

/**
 * todo@all: Documentation
 */
//{block name="backend/mopt_payone_transaction_forward/view/main/window"}
Ext.define('Shopware.apps.MoptPayoneTransactionForward.view.main.Window', {
  extend: 'Enlight.app.Window',
  title: 'Transaktionsstatus-Weiterleitung',
  cls: Ext.baseCSSPrefix + 'log-window',
  alias: 'widget.mopt-transaction-main-window',
  border: false,
  autoShow: true,
  layout: 'border',
  height: 514,
  width: 800,
  html : '<h3>Dieses Feature befindet sich noch in der Entwicklung und wird im nächsten Release enthalten sein.</h3>',

  /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
  initComponent: function() {
    var me = this;
//    me.items = [{
//      xtype: 'moptPayoneTransactionForwardMainList',
//      mappingStore: me.mappingStore
//    }];

    me.callParent(arguments);
  }
});
//{/block}