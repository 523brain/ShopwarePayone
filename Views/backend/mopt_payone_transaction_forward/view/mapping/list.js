/**
 * $Id: $
 */

//{namespace name=backend/mopt_payone_transaction_forward/main}

/**
 * Shopware UI - Log view list
 *
 * This grid contains all logs and its information.
 */
//{block name="backend/mopt_payone_transaction_forward/view/mapping/list"}
Ext.define('Shopware.apps.MoptPayoneTransactionForward.view.mapping.List',
        {
          extend: 'Ext.grid.Panel',
          border: 0,
          ui: 'shopware-ui',
          /**
           * Alias name for the view. Could be used to get an instance
           * of the view through Ext.widget('moptPayoneTransactionLogMainList')
           * @string
           */
          alias: 'widget.moptPayoneTransactionForwardMainList',
          /**
           * The window uses a border layout, so we need to set
           * a region for the grid panel
           * @string
           */
          region: 'center',
          /**
           * The view needs to be scrollable
           * @string
           */
          autoScroll: true,
          
          /**
           * Sets up the ui component
           * @return void
           */
          initComponent: function() {
            var me = this;
            me.items = me.getItems(me);

      this.columns = [
      {
        header: 'Id',  
        dataIndex: 'id',  
        flex: 1
      },
      {
        header: 'Status', 
        dataIndex: 'status', 
        flex: 1
      },
      {
        header: 'URLs', 
        dataIndex: 'urls', 
        flex: 1
      }
      ];

            me.callParent(arguments);
          },
          getItems: function(me) {
            return [
              {
                xtype: 'fieldset',
                // Standard-Eigenschaften der Kind-Elemente
                defaults: {
                  anchor: '100%'
                },
                // Titel des FieldSets
                title: '{s name=global-form/fieldsetTransMapping}Transaktionsstatusmapping{/s}',
                items: me.getPaymentStatus(),
                flex: 1
              }
            ]
          },
          getPaymentStatus: function() {

            var me = this;
            return [
              {
                xtype: 'textfield',
                fieldLabel: 'Appointed',
                name: 'stateAppointed',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Capture',
                name: 'stateCapture',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Paid',
                name: 'statePaid',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Underpaid',
                name: 'stateUnderpaid',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Cancelation',
                name: 'stateCancelation',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Refund',
                name: 'stateRefund',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Debit',
                name: 'stateDebit',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Reminder',
                name: 'stateReminder',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'VAutorisierung',
                name: 'stateVauthorization',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'VSettlement',
                name: 'stateVsettlement',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Transfer',
                name: 'stateTransfer',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
              {
                xtype: 'textfield',
                fieldLabel: 'Invoice',
                name: 'stateInvoice',
//                store: me.data.states,
                queryMode: 'local',
                displayField: 'description',
                valueField: 'id',
                allowBlank: false,
                labelWidth: 200,
              },
            ]
          }


        });
//{/block}