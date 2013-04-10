/**
 * $Id: $
 */

/**
 * Shopware - Logs store
 *
 * This store contains all logs.
 */
//{block name="backend/mopt_apilog_payone/store/logs"}
Ext.define('Shopware.apps.MoptApilogPayone.store.Logs', {

    /**
    * Extend for the standard ExtJS 4
    * @string
    */
    extend: 'Ext.data.Store',
    /**
    * Auto load the store after the component
    * is initialized
    * @boolean
    */
    autoLoad: false,
    /**
    * Amount of data loaded at once
    * @integer
    */
    pageSize: 20,
    remoteFilter: true,
    remoteSort: true,
    /**
    * Define the used model for this store
    * @string
    */
    model : 'Shopware.apps.MoptApilogPayone.model.Log',

    // Default sorting for the store
    sortOnLoad: true,
    sorters: {
        property: 'creationDate',
        direction: 'DESC'
    }
});
//{/block}