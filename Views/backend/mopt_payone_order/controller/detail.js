//{block name="backend/order/controller/detail" append}
Ext.define('Shopware.apps.Order.controller.MoptPayoneDetail', {
  override: 'Shopware.apps.Order.controller.Detail',
  
  init: function() {
    var me = this;
    me.callParent(arguments);
    
    me.control({
        'order-detail-window order-position-panel': {
          moptPayoneCapturePositions: me.onMoptPayoneCapturePositions,
          moptPayoneDebitPositions: me.onMoptPayoneDebitPositions
        }
    });
  },
  
  onMoptPayoneDebitPositions: function(order, grid, options) {
    var me = this;
    var positionIds = me.moptPayoneGetPositionIdsFromGrid(grid);
    
    if(!positionIds){
      return;
    }
    
    Ext.MessageBox.confirm('Gutschrift', 'Sind Sie sicher ?', function (response) {
      if ( response !== 'yes' ) {
        return;
      }
      
      Ext.Ajax.request({
        url: '{url controller="MoptPayoneOrder" action="moptPayoneDebit"}',
        method: 'POST',
        params: { id: order.get('id'), positionIds: Ext.JSON.encode(positionIds)},
        headers: { 'Accept': 'application/json'},
        success: function(response)
        {
          var jsonData = Ext.JSON.decode(response.responseText);
          if (jsonData.success)
          {
            Ext.Msg.alert('Success', 'Debit successful');

            //reload form
            options.callback(order);
          }
          else
          {
            Ext.Msg.alert('Failed', jsonData.error_message);
          }
        }
      });
    });
  },
          
  onMoptPayoneCapturePositions: function(order, grid, options) {
    var me = this;
    var positionIds = me.moptPayoneGetPositionIdsFromGrid(grid);
    
    if(!positionIds){
      return;
    }
    
    //bit wierd message-box... plausible way doesn't seem to work (see: http://stackoverflow.com/questions/12263291/extjs-4-or-4-1-messagebox-custom-buttons)
    Ext.MessageBox.show({
      title: 'Zahlung einziehen',
      msg: 'Welche Art des Zahlungseinzugs möchten Sie vornehmen ?',
      buttonText: { yes: '(Teil-)Capture', no: 'Finales Capture', cancel: 'Abbrechen' },
      fn: function(btn){
        if(btn === 'yes') {
          me.moptPayoneCallCapture(order, positionIds, false, options);
        } else if (btn === 'no') {
          me.moptPayoneCallCapture(order, positionIds, true, options);
        } else {
          Ext.MessageBox.hide();
        }
      }
    });
  },
  
  moptPayoneCallCapture: function(order, positionIds, finalize, options) {
    Ext.Ajax.request({
      url: '{url controller="MoptPayoneOrder" action="moptPayoneCaptureOrder"}',
      method: 'POST',
      params: { id: order.get('id'), positionIds: Ext.JSON.encode(positionIds), finalize: finalize},
      headers: { 'Accept': 'application/json'},
      success: function(response)
      {
        var jsonData = Ext.JSON.decode(response.responseText);
        if (jsonData.success)
        {
          Ext.Msg.alert('Success', 'Capture successful');
          
          //reload form
          options.callback(order);
        }
        else
        {
          Ext.Msg.alert('Failed', jsonData.error_message);
        }
      }
    });
  },
  
  moptPayoneGetPositionIdsFromGrid: function(grid) {
    
    var selectionModel = grid.getSelectionModel();
    var positions = selectionModel.getSelection();
    var positionIds = [];
    
    if (positions.length === 0) {
      return;
    }
    
    for (var i = 0; i < positions.length; i++)
    {
      positionIds.push(positions[i].get('id'));
    }
    
    return positionIds;
  }
});
//{/block}