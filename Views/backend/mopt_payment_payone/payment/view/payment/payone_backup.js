/**
 *
 */
Ext.define('Shopware.apps.MoptPaymentPayone.payment.view.payment.FormPanel', {
  extend : 'Ext.form.Panel',
  autoShow: true,
  alias : 'widget.payment-payone-formpanel',
  region: 'center',
  layout: 'anchor',
  autoScroll: true,
  bodyPadding: '10px',
  name:  'formpanel',
  preventHeader: true,
  border: 0,
  defaults:{
    labelStyle:'font-weight: 700; text-align: right;',
    labelWidth:130,
    anchor:'100%'
  },
  plugins: [{
//    pluginId: 'translation',
//    ptype: 'translation',
//    translationType: 'config_payment',
//    translationMerge: true
  }],

  /**
     * This function is called, when the component is initiated
     * It creates the columns of the grid
     */
  initComponent: function(){
    var me = this;
    me.items = me.getItems();
    me.callParent(arguments);
  },

  /**
     * This function creates the columns of the grid
     * @return Array
     */
  getItems: function(){
    var items = [{
      xtype: 'checkbox',
      fieldLabel: 'Live Mode',
      inputValue: 1,
      uncheckedValue: 0,
      name: 'active'
    },{
      xtype: 'checkbox',
      fieldLabel: 'Global Config',
      inputValue: 1,
      uncheckedValue: 0,
      name: 'esdActive'
    },{
      xtype: 'checkbox',
      fieldLabel: 'Consumerscore',
      inputValue: 1,
      uncheckedValue: 0,
      name: 'esdActive'
    }
    ];

    return items;
  }
});