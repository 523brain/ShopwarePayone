/**
 * $Id: $
 */

/**
 * Shopware - Users store
 *
 * This store contains all users.
 */
//{block name="backend/mopt_apilog_payone/store/users"}
Ext.define('Shopware.apps.MoptApilogPayone.store.Users', {

    /**
    * Extend for the standard ExtJS 4
    * @string
    */
    extend: 'Shopware.apps.Base.store.User',
    /**
    * Amount of data loaded at once
    * @integer
    */
    pageSize: 2000
});
//{/block}