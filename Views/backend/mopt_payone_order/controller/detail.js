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
    
    var selectionModel = grid.getSelectionModel();
    var positions = selectionModel.getSelection();
    var amount = 0;
    
    
    for (var i = 0; i < positions.length; i++)
    {
      amount+=positions[i].get('total');
    }
    
    Ext.MessageBox.confirm('Gutschrift', 'Sie haben ' + positionIds.length + ' Position(en) mit einem Gesamtbetrag von <span style="color: red;">' + amount.toFixed(2) + '&#8364 </span>markiert. <br> Sind Sie sicher ?', function (response) {
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
            Ext.Msg.alert('Gutschrift', 'Die Gutschrift wurde erfolgreich durchgeführt.');

            //reload form
            options.callback(order);
          }
          else
          {
            Ext.Msg.alert('Gutschrift', jsonData.error_message);
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
    
    var selectionModel = grid.getSelectionModel();
    var positions = selectionModel.getSelection();
    var amount = 0;
    
    
    for (var i = 0; i < positions.length; i++)
    {
      amount+=positions[i].get('total');
    }
    
    
    //bit wierd message-box... plausible way doesn't seem to work (see: http://stackoverflow.com/questions/12263291/extjs-4-or-4-1-messagebox-custom-buttons)
    Ext.MessageBox.show({
      title: 'Zahlung einziehen',
      msg: 'Sie haben ' + positionIds.length + ' Position(en) mit einem Gesamtbetrag von <span style="color: red;">' + amount.toFixed(2) + '&#8364 </span> markiert. <br> Welche Art des Zahlungseinzugs möchten Sie vornehmen ?',
      buttonText: { yes: '(Teil-)Geldeinzug', no: 'Finaler Geldeinzug', cancel: 'Abbrechen' },
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
          Ext.Msg.alert('Geldeinzug', 'Der Geldeinzug wurde erfolgreich durchgeführt.');
          
          //reload form
          options.callback(order);
        }
        else
        {
          Ext.Msg.alert('Geldeinzug', jsonData.error_message);
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