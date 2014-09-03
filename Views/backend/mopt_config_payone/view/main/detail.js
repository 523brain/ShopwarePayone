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
    return [
      {
        xtype: 'combobox',
        fieldLabel: 'Gilt für Zahlart:',
        store: payments,
        displayField: 'description',
        tpl: Ext.create('Ext.XTemplate',
            '<tpl for=".">',
                '<tpl if="this.doHighlight(configSet)">',
                    '<div class="x-boundlist-item" style="background-color:#f08080;">{literal}{description}{/literal}</div>',
                '<tpl else>',
                    '<div class="x-boundlist-item">{literal}{description}{/literal}</div>',
                '</tpl>',
            '</tpl>',
            {
                doHighlight: function(configSet) {
                  if(configSet === 1)
                  {
                    return true;
                  }
                  else
                  {
                    return false;
                  }
                }
            }
        ),
        valueField: 'id',
        name: 'paymentId',
        width: 600,
        allowBlank: true,
        value: 0
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
            success: function(form, action) {
              Ext.Msg.alert('Zahlartkonfiguration zurücksetzen', action.result.data);
              payments.reload();
            },
            failure: function() {
              Ext.Msg.alert('Zahlartkonfiguration zurücksetzen', 'Die Daten wurden nicht zurückgesetzt.');
            },
            waitTitle: 'Bitte warten...',
            waitMsg: 'Daten werden verarbeitet'
          });
        }
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
            success: function(form, action) {
              Ext.Msg.alert('Zahlartkonfiguration speichern', action.result.data);
              payments.reload();
            },
            failure: function() {
              Ext.Msg.alert('Zahlartkonfiguration speichern', 'Die Daten konnten nicht gespeichert werden. Bitte überprüfen Sie ihre Eingaben.');
            },
            waitTitle: 'Bitte warten...',
            waitMsg: 'Daten werden verarbeitet'
          });
        }
      },
      {
        xtype: 'tabpanel',
        items: fieldsets,
        renderTo: document.body,
        width: 880,
        height: 820
      }];
  },
  activateField: function(me, field) {
    tabs = me.items.getAt(3);
    fieldset = tabs.items.getAt(0);
    if (!field) {
      fieldset.items.getAt(8).enable();
      fieldset.items.getAt(9).disable();
    }
    else
    if (field === 'debit') {
      fieldset.items.getAt(8).disable();
      fieldset.items.getAt(9).enable();
    } else {
      fieldset.items.getAt(8).disable();
      fieldset.items.getAt(9).disable();
    };
    if (field === 'klarna') {
      fieldset.items.getAt(13).enable();
    }
    else
    {
      fieldset.items.getAt(13).disable();
    };
  },
  
  /**
   * creates form child elements
   * grouped in Ext.form.FieldSet
   */
  getFieldSets: function() {
    var me = this;
    return [
      {
        xtype: 'fieldset',
        defaults: {
          anchor: '100%'
        },
        title: '{s name=global-form/fieldset1}Allgemein{/s}',
        items: me.getGlobalSetItems(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        defaults: {
          anchor: '100%'
        },
        title: '{s name=global-form/fieldset2}Adressüberprüfung{/s}',
        items: me.getRiskSetItems(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        defaults: {
          anchor: '100%'
        },
        title: '{s name=global-form/fieldset3}Bonitätsprüfung{/s}',
        items: me.getConsumerSetItems(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        defaults: {
          anchor: '100%'
        },
        title: '{s name=global-form/fieldset4}Paymentstatus{/s}',
        items: me.getPaymentStatus(),
        flex: 1
      },
      {
        xtype: 'fieldset',
        defaults: {
          anchor: '100%'
        },
        title: '{s name=global-form/fieldset5}Transaktionsstatusweiterleitung{/s}',
        items: me.getTransactionMapping(),
        flex: 1
      },
    ];
  },
  
  getTransactionMapping: function() {

    var me = this;
    return [
      {
        xtype: 'label',
        text: 'Mehrere URLs können duch ; getrennt angegeben werden.',
        margin: '0 0 10 0',
        style: {
          display: 'block !important'
        }
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Appointed',
        name: 'transAppointed',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Capture',
        name: 'transCapture',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Paid',
        name: 'transPaid',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Underpaid',
        name: 'transUnderpaid',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Cancelation',
        name: 'transCancelation',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Refund',
        name: 'transRefund',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Debit',
        name: 'transDebit',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Reminder',
        name: 'transReminder',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'VAutorisierung',
        name: 'transVauthorization',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'VSettlement',
        name: 'transVsettlement',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Transfer',
        name: 'transTransfer',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Invoice',
        name: 'transInvoice',
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: true,
        labelWidth: 200
      }
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Minimaler Warenwert',
        name: 'adresscheckMinBasket',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Maximaler Warenwert',
        name: 'adresscheckMaxBasket',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Gültigkeit',
        name: 'adresscheckLifetime',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Fehlermeldung',
        name: 'adresscheckFailureMessage',
        disabled: true,
        helpText: 'Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach addrescheckErrorMessage suchen)',
        allowBlank: true,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Keine Personenüberprüfung durchgeführt',
        name: 'mapPersonCheck',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Vor- und Nachname bekannt',
        name: 'mapKnowPreLastname',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Nachname bekannt',
        name: 'mapKnowLastname',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Vor- und Nachname nicht bekannt',
        name: 'mapNotKnowPreLastname',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Mehrdeutigkeit bei Name zu Anschrift',
        name: 'mapMultiNameToAdress',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'nicht (mehr) zustellbar',
        name: 'mapUndeliverable',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Person verstorben',
        name: 'mapPersonDead',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Adresse postalisch falsch',
        name: 'mapWrongAdress',
        store: me.data.signal,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
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
        labelWidth: 200
      },
      {
        xtype: 'numberfield',
        name: 'consumerscoreLifetime',
        fieldLabel: 'Gültigkeit',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Minimaler Warenwert',
        name: 'consumerscoreMinBasket',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'numberfield',
        fieldLabel: 'Maximaler Warenwert',
        name: 'consumerscoreMaxBasket',
        allowBlank: false,
        labelWidth: 200
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
        labelWidth: 200
      },
      {
        xtype: 'fieldcontainer',
        fieldLabel: 'Hinweistext',
        labelWidth: 200,
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
            disabled: true,
            helpText: 'Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach consumerscoreNoteMessage suchen)',
            width: '100%'
          }
        ]
      },
      {
        xtype: 'fieldcontainer',
        fieldLabel: 'Zustimmungsfrage',
        labelWidth: 200,
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
            disabled: true,
            helpText: 'Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach consumerscoreAgreementMessage suchen)',
            width: '100%'
          }
        ]
      },
      {
        xtype: 'fieldcontainer',
        fieldLabel: 'A/B Test',
        labelWidth: 200,
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
   * helper function to create form elements of field set
   */
  getGlobalSetItems: function() {
    var me = this;
    return [
      {
        xtype: 'hidden',
        fieldLabel: 'id',
        name: 'id'
      },
      {
        //creates Ext.form.field.Text input field
        xtype: 'textfield',
        fieldLabel: 'Merchant-ID',
        helpText: 'ID des zu verwendenden Accounts',
        name: 'merchantId',
        allowBlank: false,
        labelWidth: 200
      },
      {
        //creates Ext.form.field.Text input field
        xtype: 'textfield',
        fieldLabel: 'Portal-ID',
        helpText: 'ID des zu verwendenden Zahlungsportal',
        name: 'portalId',
        allowBlank: false,
        labelWidth: 200
      },
      {
        //creates Ext.form.field.Text input field
        xtype: 'textfield',
        fieldLabel: 'Subaccount-ID',
        helpText: 'ID des zu verwendenden SubAccounts',
        name: 'subaccountId',
        allowBlank: false,
        labelWidth: 200
      },
      {
        //creates Ext.form.field.Text input field
        xtype: 'textfield',
        fieldLabel: 'Schlüssel',
        helpText: 'Schlüssel des zu verwendenden Zahlungsportal',
        name: 'apiKey',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Betriebsmodus',
        helpText: 'Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert',
        store: me.data.testlive,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        name: 'liveMode',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Autorisierung',
        helpText: 'Die Vorautorisation ist die Eröffnung eines Zahlvorgangs auf der PAYONE-Plattform. Wenn die Zahlart es zulässt wird eine Reservierung des Betrages durchgeführt. Bei Zahlarten wie Sofortüberweisung.de wird der Betrag sofort eingezogen weil dort keine Reservierung durchgeführt werden kann. Bei Zahlarten wie z.B. Vorkasse oder Rechnung wird der Zahlvorgang nur auf der PAYONE – Plattform angelegt. Wenn die Autorisation durchgeführt wird, dann wird wenn möglich der Betrag sofort eingezogen',
        name: 'authorisationMethod',
        store: me.data.auth,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Warenkorbübergabe',
        helpText: 'Soll der Warenkorbinhalt an PAYONE übermittelt werden?',
        name: 'submitBasket',
        store: me.data.submitbasket,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Abfrage Kreditkartenprüfziffer<br>(nur global konfigurierbar)',
        name: 'checkCc',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        disabled: false,
        labelWidth: 200,
        value: 'true'
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
        disabled: true,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Zusätzlich Kontonummer/Bankleitzahl anzeigen?',
        helpText: 'Nur bei deutschen Konten können zusätzlich Kontonummer/Bankleitzahl angezeigt werden.',
        name: 'showAccountnumber',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        disabled: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Mandatserteilung aktivieren?',
        helpText: 'Die Mandatserteilung erfolgt mit dem kosteplfichtigen Request "managemandate". Dieser Request beinhaltet einen bankaccountcheck. Allerdings ist hier keine Abfrage der POS-Sperrliste möglich.',
        name: 'mandateActive',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        disabled: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Download Mandat als PDF?',
        helpText: 'Diese Option kann nur ausgewählt werden, wenn bei PAYONE das Produkt "SEPA-Mandate als PDF" gebucht wurde.',
        name: 'mandateDownloadEnabled',
        store: me.data.yesno,
        queryMode: 'local',
        displayField: 'display',
        valueField: 'value',
        allowBlank: false,
        disabled: false,
        labelWidth: 200
      },
      {
        xtype: 'textfield',
        fieldLabel: 'Klarna Store-ID',
        helpText: 'Klarna Store-ID',
        name: 'klarnaStoreId',
        allowBlank: true,
        disabled: true,
        labelWidth: 200
      }
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
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Capture',
        name: 'stateCapture',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Paid',
        name: 'statePaid',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Underpaid',
        name: 'stateUnderpaid',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Cancelation',
        name: 'stateCancelation',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Refund',
        name: 'stateRefund',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Debit',
        name: 'stateDebit',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Reminder',
        name: 'stateReminder',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'VAutorisierung',
        name: 'stateVauthorization',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'VSettlement',
        name: 'stateVsettlement',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Transfer',
        name: 'stateTransfer',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
      {
        xtype: 'combobox',
        fieldLabel: 'Invoice',
        name: 'stateInvoice',
        store: me.data.states,
        queryMode: 'local',
        displayField: 'description',
        valueField: 'id',
        allowBlank: false,
        labelWidth: 200
      },
    ]
  }
});
//{/block}
