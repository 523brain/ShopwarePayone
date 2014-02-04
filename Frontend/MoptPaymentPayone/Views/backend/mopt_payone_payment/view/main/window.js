//{block name="backend/payment/view/main/window" append}
Ext.define('Shopware.apps.Payment.view.main.MoptPayoneWindow', {
  override: 'Shopware.apps.Payment.view.main.Window',
  
    createToolbar: function() {
    var me = this;
    var toolbar = me.callParent(arguments);
    
    me.moptPayoneDuplicatePaymentButton = Ext.create('Ext.button.Button', {
      text: 'Zahlart duplizieren',
      cls: 'primary',
      action: 'moptPayoneDuplicatePayment',
      handler: function() {
        me.fireEvent('moptPayoneDuplicatePayment', me.generalForm)
      }
    });
    
    toolbar.items.add(me.moptPayoneDuplicatePaymentButton);
    
    return toolbar;
  },
  
    registerEvents: function() {
    var me = this;
    me.callParent(arguments);
    
    this.addEvents('moptPayoneDuplicatePayment');
    }
  
});
//{/block}