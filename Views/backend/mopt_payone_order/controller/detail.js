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
    
    var details = order.raw.details;
    var showShippingCostsCheckbox = true;
    
    for (var i = 0; i < details.length; i++)
    {
      if (details[i].articleNumber === "SHIPPING")
      {
        showShippingCostsCheckbox = false;
      }
    }
    
    var moptMessageBoxText =  '<p>Sie haben ' + positionIds.length + ' Position(en) mit einem Gesamtbetrag von ' 
            + '<span style="color: red;">' + amount.toFixed(2) + '&#8364 </span>markiert.</p><br>' 
            + '<p><label for="mopt_payone__capture_shipment">Versandkosten mit gutschreiben</label>' 
            + '<input type="checkbox" id="mopt_payone__debit_shipment" class="x-form-field x-form-checkbox"' 
            + 'style="margin: 0 0 0 4px; height: 15px !important; width: 15px !important;"/></p>' 
            + '<br><p>Sind Sie sicher ?</p>';
    
    if(!showShippingCostsCheckbox)
    {
      moptMessageBoxText =  'Sie haben ' + positionIds.length + ' Position(en) mit einem Gesamtbetrag von ' 
            + '<span style="color: red;">' + amount.toFixed(2) + '&#8364 </span>markiert. ' 
            + '<br> Sind Sie sicher ?';
    }
    
    Ext.MessageBox.confirm('Gutschrift', 
    moptMessageBoxText, function (response) {
      if ( response !== 'yes' ) {
        return;
      }
      var includeShipment = false;
      
      
      if (showShippingCostsCheckbox && Ext.get('mopt_payone__debit_shipment').dom.checked)
      {
				includeShipment = true;
			} 
      
      Ext.Ajax.request({
        url: '{url controller="MoptPayoneOrder" action="moptPayoneDebit"}',
        method: 'POST',
        params: { id: order.get('id'), positionIds: Ext.JSON.encode(positionIds), includeShipment: includeShipment},
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
    
    var details = order.raw.details;
    var showShippingCostsCheckbox = true;
    
    for (var i = 0; i < details.length; i++)
    {
      if (details[i].articleNumber === "SHIPPING")
      {
        showShippingCostsCheckbox = false;
      }
    }
    
    var moptMessageBoxText =  '<p>Sie haben ' + positionIds.length 
            + ' Position(en) mit einem Gesamtbetrag von <span style="color: red;">' 
              + amount.toFixed(2) + '&#8364 </span> markiert.</p><br>' 
              + '<p><label for="mopt_payone__capture_shipment">Versandkosten auch Einziehen</label>' 
              + '<input type="checkbox" id="mopt_payone__capture_shipment" class="x-form-field x-form-checkbox"' 
              + 'style="margin: 0 0 0 4px; height: 15px !important; width: 15px !important;"/>'
              + '</p><br>' 
              + '<p>Welche Art des Zahlungseinzugs möchten Sie vornehmen ?</p>';
    
    if(!showShippingCostsCheckbox)
    {
      moptMessageBoxText =  'Sie haben ' + positionIds.length 
              + ' Position(en) mit einem Gesamtbetrag von <span style="color: red;">' 
              + amount.toFixed(2) + '&#8364 </span> markiert. <br>' 
              + ' Welche Art des Zahlungseinzugs möchten Sie vornehmen ?';
    }
    
    //bit wierd message-box... plausible way doesn't seem to work 
    //(see: http://stackoverflow.com/questions/12263291/extjs-4-or-4-1-messagebox-custom-buttons)
    Ext.MessageBox.show({
      title: 'Zahlung einziehen',
      msg: moptMessageBoxText,
      buttonText: { yes: '(Teil-)Geldeinzug', no: 'Finaler Geldeinzug', cancel: 'Abbrechen' },
      fn: function(btn){
        
        var includeShipment = false;
        
         if (showShippingCostsCheckbox && Ext.get('mopt_payone__capture_shipment').dom.checked)
         {
          includeShipment = true;
         } 
        
        if(btn === 'yes') {
          me.moptPayoneCallCapture(order, positionIds, false, options, includeShipment);
        } else if (btn === 'no') {
          me.moptPayoneCallCapture(order, positionIds, true, options, includeShipment);
        } else {
          Ext.MessageBox.hide();
        }
      }
    });
  },
  
  moptPayoneCallCapture: function(order, positionIds, finalize, options, includeShipment) {
    Ext.Ajax.request({
      url: '{url controller="MoptPayoneOrder" action="moptPayoneCaptureOrder"}',
      method: 'POST',
      params: { id: order.get('id'), 
                positionIds: Ext.JSON.encode(positionIds), 
                finalize: finalize, 
                includeShipment: includeShipment},
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