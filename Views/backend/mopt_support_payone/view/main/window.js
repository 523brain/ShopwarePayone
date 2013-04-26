/**
* $Id: $
 */

//{namespace name=backend/mopt_support_payone/view/main}

//{block name="backend/mopt_support_payone/view/main/window"}
Ext.define('Shopware.apps.MoptSupportPayone.view.main.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name=mopt_support_payone/window/title}Hilfe & Support{/s}',
    alias: 'widget.mopt-support-main-window',
    border: false,
    layout: 'fit',
    autoShow: true,
    height: '90%',
    width: 1024,
    stateful: true,
    stateId: 'mopt-support-main-window',

    /**
     * Property which represents the iframe "src"-URL
     * @string
     */
    requestUrl: 'http://www.payone.de/',

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.items = [{
            xtype: 'container',
            // We need to hack the iframe to inject it into the Ext.window.Window
            html: '<ifr' + 'ame id="iframe-' + Ext.id() + '" border="0" src="'+ me.requestUrl +'"></ifr' + 'ame>'
        }];
        me.callParent(arguments);
    }
});
//{/block}