//{block name="backend/payment/controller/payment" append}
Ext.define('Shopware.apps.Payment.controller.MoptPayonePayment', {
  override: 'Shopware.apps.Payment.controller.Payment',
  
  init: function() {
    var me = this;
    
    me.control({
      'payment-main-window': {
        moptPayoneDuplicatePayment: me.onMoptPayoneDuplicatePayment
      }
    });
    
    me.callParent(arguments);
  },
  
  onMoptPayoneDuplicatePayment: function(generalForm) {
    var me = this,
    record = generalForm.getRecord(),
    paymentId = record.get('id'),
    paymentName = record.get('description'),
    paymentStore = me.subApplication.paymentStore;
      
    Ext.Ajax.request({
      url: '{url controller="MoptPayonePayment" action="moptPayoneDuplicatePayment"}',
      method: 'POST',
      params: {
        id: paymentId
      },
      headers: {
        'Accept': 'application/json'
      },
      success: function(response)
      {
        var jsonData = Ext.JSON.decode(response.responseText);
        if (jsonData.success)
        {
          Ext.Msg.alert('Zahlart Duplizieren', 'Die Zahlart \"' + paymentName + '\" wurde erfolgreich dupliziert');

          //reload form
          options.callback(record);
        }
        else
        {
          Ext.Msg.alert('Zahlart Duplizieren', jsonData.error_message);
        }
      }
    });
    paymentStore.load();
  }
  
  
});
//{/block}