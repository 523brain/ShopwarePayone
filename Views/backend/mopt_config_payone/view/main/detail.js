//{namespace name=backend/mopt_config_payone/main}
//{block name="backend/mopt_config_payone/view/main/detail"}
Ext.define('Shopware.apps.MoptConfigPayone.view.main.Detail', {
  extend: 'Ext.form.Panel',
  alias: 'widget.mopt-config-main-detail',
  autoScroll: true,
  cls: 'shopware-form',
  layout: 'anchor',
  border: false,
  url: '{url action=saveConfig}',
  bodyPadding: 10,
  /**
   * Called when the component will be initialed.
   */
  initComponent: function() {
    var me = this;

    me.items = me.createTabpanel(me.getFieldSets(), me.data.payments);
    me.callParent(arguments);

    me.data.signal = Ext.create('Shopware.apps.MoptConfigPayone.store.ComboSignal');

    me.items.getAt(0).addListener('select', function(data) {
      var id = data.getValue();
      data = me.data.config.load({
        filters: [{
            property: 'payment_id',
            value: id
          }],
        limit: 1,
        action: 'payment',
        callback: function(records) {
          me.activateField(me, records[0].data.extra);
          me.loadRecord(records[0]);
        }
      });
    });

    me.data.config.load({
      callback: function(records) {
        me.activateField(me, records[0].data.extra);
        me.loadRecord(records[0]);
      }
    });




  },
  createTabpanel: function(fieldsets, payments) {
    var me = this;
    return [{
        xtype: 'combobox',
        fieldLabel: 'Gilt für Zahlart:',
        store: payments,
        displayField: 'description',
        valueField: 'id',
        name: 'paymentId',
        allowBlank: true,
      },
      {
        xtype: 'button',
        text: '{s name=global-form/resetbutton}Zurücksetzen{/s}',
        iconCls: 'sprite-tick-circle',
        name: 'reset',
        handler: function(a, b) {
          me.submit({
            params: {
              type: 'reset'
            },
//            url: '',
            success: function(form, action) {
              Ext.Msg.alert('Success', action.result.data);
            },
          });
        },
      },
      {
        xtype: 'button',
        text: '{s name=global-form/button}Speichern{/s}',
        iconCls: 'sprite-tick-circle',
        name: 'save',
        handler: function(a, b) {
          me.submit({
            params: {
              type: 'save'
            },
//            url: '',
            success: function(form, action) {
              Ext.Msg.alert('Success', action.result.data);
            },
          });
        },
      },
      {
        xtype: 'tabpanel',
        items: fieldsets,
        renderTo: document.body,
        width: 880,
        height: 620,
      }];
  },
  activateField: function(me, field) {
    tabs = me.items.getAt(3);
    fieldset = tabs.items.getAt(0);
    if (field === 'cc') {
      fieldset.items.getAt(8).enable();
      fieldset.items.getAt(9).disable();
    } else if (field === 'debit') {
      fieldset.items.getAt(8).disable();
      fieldset.items.getAt(9).enable();
    } else {
      fieldset.items.getAt(8).disable();
      fieldset.items.getAt(9).disable();
    }
  },
  /**
   * Erzeugt die Kind-Elemente des Formulars.
   * In diesem Fall werden die Kind-Elemente in einem
   * Ext.form.FieldSet gruppiert.
   */
  getFieldSets: function() {
    var me = this;

    return [
      {
        xtype: 'fieldset',
        // Standard-Eigenschaften der Kind-Elemente
        defaults: {
          anchor: '100%'
        },
        // Titel des FieldSets
        title: '{s name=global-form/fieldset1}Allgemein{/s}',
        items: me.getGlobalSetItems(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        // Standard-Eigenschaften der Kind-Elemente
        defaults: {
          anchor: '100%'
        },
        // Titel des FieldSets
        title: '{s name=global-form/fieldset2}Adressüberprüfung{/s}',
        items: me.getRiskSetItems(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        // Standard-Eigenschaften der Kind-Elemente
        defaults: {
          anchor: '100%'
        },
        // Titel des FieldSets
        title: '{s name=global-form/fieldset3}Bonitätsprüfung{/s}',
        items: me.getConsumerSetItems(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        // Standard-Eigenschaften der Kind-Elemente
        defaults: {
          anchor: '100%'
        },
        // Titel des FieldSets
        title: '{s name=global-form/fieldset4}Paymentstatus{/s}',
        items: me.getPaymentStatus(),
        flex: 1
      },
      
    ];
  },
  getRiskSetItems: function() {
    var me = this;

    return [
      {
        xtype: 'combobox',
        fieldLabel: 'Aktiv',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'adresscheckActive',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Betriebsmodus',
        store: me.data.testlive,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'adresscheckLiveMode',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Rechnungsadresse',
        store: me.data.checkbasicperson,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'adresscheckBillingAdress',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Lieferadresse',
        store: me.data.checkbasicperson,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'adresscheckShippingAdress',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Automatische Korrektur',
        store: me.data.yesnouser,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'adresscheckAutomaticCorrection',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Fehlverhalten',
        store: me.data.mistake,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'adresscheckFailureHandling',
        allowBlank: false,
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Minimaler Warenwert',
        name: 'adresscheckMinBasket',
        allowBlank: false
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Maximaler Warenwert',
        name: 'adresscheckMaxBasket',
        allowBlank: false
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Gültigkeit',
        name: 'adresscheckLifetime',
        allowBlank: false
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Fehlermeldung',
        name: 'adresscheckFailureMessage',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Keine Personenüberprüfung durchgeführt',
        name: 'mapPersonCheck',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Vor- und Nachname bekannt',
        name: 'mapKnowPreLastname',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Nachname bekannt',
        name: 'mapKnowLastname',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Vor- und Nachname nicht bekannt',
        name: 'mapNotKnowPreLastname',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Mehrdeutigkeit bei Name zu Anschrift',
        name: 'mapMultiNameToAdress',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'nicht (mehr) zustellbar',
        name: 'mapUndeliverable',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Person verstorben',
        name: 'mapPersonDead',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Adresse postalisch falsch',
        name: 'mapWrongAdress',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
    ];
  },
  getConsumerSetItems: function() {

    var me = this;

    return [
      {
        xtype: 'combobox',
        fieldLabel: 'Aktiv',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'consumerscoreActive',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Betriebsmodus',
        store: me.data.testlive,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'consumerscoreLiveMode',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Zeitpunkt der Prüfung',
        store: me.data.point,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'consumerscoreCheckMoment',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Prüfungsart',
        store: me.data.infoscore,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'consumerscoreCheckMode',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Standardwert für Neukunden',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'consumerscoreDefault',
        allowBlank: false,
      },
      {
        xtype: 'numberfield',
        name: 'consumerscoreLifetime',
        fieldLabel: 'Gültigkeit',
        allowBlank: false
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Minimaler Warenwert',
        name: 'consumerscoreMinBasket',
        allowBlank: false
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Maximaler Warenwert',
        name: 'consumerscoreMaxBasket',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Fehlverhalten',
        store: me.data.consumerscore,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'consumerscoreFailureHandling',
        allowBlank: false,
      },
      {
        xtype: 'fieldcontainer',
        fieldLabel: 'Hinweistext',
        layout: 'vbox',
        items: [
          {
            xtype: 'checkboxfield',
            name: 'consumerscoreNoteActive',
            checked: '1',
            boxLabel: 'Aktiv'
          }, {
            xtype: 'textfield',
            name: 'consumerscoreNoteMessage',
            width: '100%'
          }
        ]
      },
      {
        xtype: 'fieldcontainer',
        fieldLabel: 'Zustimmungsfrage',
        layout: 'vbox',
        items: [
          {
            xtype: 'checkboxfield',
            name: 'consumerscoreAgreementActive',
            checked: 1,
            boxLabel: 'Aktiv'
          }, {
            xtype: 'textfield',
            name: 'consumerscoreAgreementMessage',
            width: '100%'
          }
        ]
      },
      {
        xtype: 'fieldcontainer',
        fieldLabel: 'A/B Test',
        layout: 'vbox',
        items: [
          {
            xtype: 'checkboxfield',
            name: 'consumerscoreAbtestActive',
            checked: '1',
            boxLabel: 'Aktiv'
          }, {
            xtype: 'numberfield',
            name: 'consumerscoreAbtestValue',
            width: '100%'
          }
        ]
      }
    ]
  },
  /**
   * Helfer-Funktion, welche die Form-Elemente des FieldSets erzeugt
   */
  getGlobalSetItems: function() {
    var me = this;
    return [
      {
        xtype: 'hidden',
        fieldLabel: 'id',
        name: 'id',
      },
      {
        // Erzeugt ein Ext.form.field.Text-Eingabefeld
        xtype: 'textfield',
        fieldLabel: 'Merchant-ID',
        name: 'merchantId',
        allowBlank: false
      },
      {
        // Erzeugt ein Ext.form.field.Text-Eingabefeld
        xtype: 'textfield',
        fieldLabel: 'Portal-ID',
        name: 'portalId',
        allowBlank: false
      },
      {
        // Erzeugt ein Ext.form.field.Text-Eingabefeld
        xtype: 'textfield',
        fieldLabel: 'Subaccount-ID',
        name: 'subaccountId',
        allowBlank: false
      },
      {
        // Erzeugt ein Ext.form.field.Text-Eingabefeld
        xtype: 'textfield',
        fieldLabel: 'Schlüssel',
        name: 'apiKey',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Betriebsmodus',
        store: me.data.testlive,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'liveMode',
        allowBlank: false,
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Autorisierung',
        name: 'authorisationMethod',
        store: me.data.auth,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Warenkorbübergabe',
        name: 'submitBasket',
        store: me.data.submitbasket,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Abfrage Kartenprüfziffer',
        name: 'checkCc',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        disabled: true
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Bankdaten überprüfen',
        name: 'checkAccount',
        store: me.data.checkcc,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        disabled: true
      },
    ]
  },
  getPaymentStatus: function() {

    var me = this;
    return [
      {
        xtype: 'combobox',
        fieldLabel: 'Appointed',
        name: 'stateAppointed',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Capture',
        name: 'stateCapture',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Paid',
        name: 'statePaid',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Underpaid',
        name: 'stateUnderpaid',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Cancelation',
        name: 'stateCancelation',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Refund',
        name: 'stateRefund',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Debit',
        name: 'stateDebit',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Reminder',
        name: 'stateReminder',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'VAutorisierung',
        name: 'stateVauthorization',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'VSettlement',
        name: 'stateVsettlement',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Transfer',
        name: 'stateTransfer',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Invoice',
        name: 'stateInvoice',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false
      },
    ]
  }
});
//{/block}
