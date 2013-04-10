/**
 * $Id: $
 */

//{namespace name=backend/mopt_apilog_payone/main}

/**
 * todo@all: Documentation
 */
//{block name="backend/mopt_apilog_payone/view/main/detailwindow"}
Ext.define('Shopware.apps.MoptApilogPayone.view.main.Detailwindow', {
	extend: 'Enlight.app.Window',
    title: '{s name=window_title}Response-Request Informationen{/s}',
    cls: Ext.baseCSSPrefix + 'detail-window',
    alias: 'widget.moptPayoneApilogMainDetailWindow',
    border: false,
    autoShow: true,
    layout: 'border',
    height: '90%',
    width: 800,

    stateful: true,
    stateId:'shopware-detail-window',

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.items = [{
            xtype: 'moptPayoneApilogMainDetail',
            itemSelected: me.itemSelected,
        }];

        me.callParent(arguments);
    }
});
//{/block}