/**
* $Id: $
 */

//{namespace name=backend/mopt_export_payone/view/main}
//{block name="backend/mopt_export_payone/controller/main"}
Ext.define('Shopware.apps.MoptExportPayone.controller.Main', {

  /**
     * Extend from the standard ExtJS 4 controller
     * @string
     */
  extend: 'Ext.app.Controller',

  /**
	 * Creates the necessary event listener for this
	 * specific controller and opens a new Ext.window.Window
	 * to display the subapplication
     *
     * @return void
	 */
  init: function() {
    var me = this;
    me.mainWindow = me.getView('main.Window').create();
    
    me.control({
      'mopt-export-config-main-window': {
          moptDownloadConfigExport: me.onMoptDownloadConfigExport
      }
    });
  },
  
  onMoptDownloadConfigExport: function(view) {

    var loadMask = new Ext.LoadMask(Ext.getBody(), {
        msg: '{s name=loadmask}Erstelle Konfigurationsexport...{/s}',
        removeMask: true
    });

    loadMask.show();

    Ext.Ajax.request({
        url: '{url controller="MoptExportPayone" action="generateConfigExport"}',
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        success: function(response)
        {
            loadMask.hide();
            loadMask.disable();

            var jsonData = Ext.JSON.decode(response.responseText);
            if (jsonData.success)
            {
              var date = new Date();
              var month = date.getMonth() + 1;
              var moptConfigDownloadLink = document.createElement('a');
              moptConfigDownloadLink.setAttribute('href', 'data:text/xml;charset=utf-8,' + encodeURIComponent(jsonData.moptConfigExport));
              moptConfigDownloadLink.setAttribute('download', 'PayoneConfiguration' + date.getDate() + month + date.getFullYear() + '.xml');
              moptConfigDownloadLink.click();
              moptConfigDownloadLink.remove();
            }
            else
            {
                Ext.Msg.alert('{s name=download/error}Download fehlgeschlagen{/s}', jsonData.error_message);
            }
        }
    });
}
});
//{/block}