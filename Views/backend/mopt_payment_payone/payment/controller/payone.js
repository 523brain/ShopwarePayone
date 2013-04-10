//{block name="backend/payment/controller/payment" append}
Ext.override(Shopware.apps.Payment.controller.Payment, {
  /**
     * Is fired, when the tab is changed
     * Automatically selects the countries/shops and sets the surcharge
     * @param tabPanel Contains the tabpanel
     * @param newTab Contains the new tab, which was clicked now
     * @param oldTab Contains the old tab, which was opened before the new tab
     * @param formPanel Contains the general formpanel
     */
  onChangeTab:function(tabPanel, newTab, oldTab, formPanel){
    var grid = newTab.items.items[0],
    record = formPanel.getRecord(),
    recordStore;

    switch (grid.xtype) {
      //The formpanel and the surcharge-grid mustn't be affected
      case 'payment-main-formpanel':
        return;
        break;
      case 'payment-payone-formpanel':
        return;
        break;
      case 'payment-main-surcharge':
        return;
        break;
      case 'payment-main-countrylist':
        recordStore = record.getCountriesStore;
        break;
      case 'payment-main-subshoplist':
        recordStore = record.getShopsStore;
        break;
    }

    var store = grid.getStore().load({
      callback: function(){
        var matches = [];
        //Selects each country and sets the surcharge
        if(recordStore){
          Ext.each(recordStore.data.items, function(item){
            var tmpRecord = store.getById(item.get('id'));
            matches.push(tmpRecord);
            tmpRecord.data.surcharge = item.get('surcharge');
          });
          grid.getSelectionModel().select(matches);
        }
      }
    });
  }
});
//{/block}