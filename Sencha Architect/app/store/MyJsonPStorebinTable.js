/*
 * File: app/store/MyJsonPStorebinTable.js
 *
 * This file was generated by Sencha Architect version 3.0.2.
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 4.2.x library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 4.2.x. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('Clientv1.store.MyJsonPStorebinTable', {
    extend: 'Ext.data.Store',

    requires: [
        'Clientv1.model.binTableModel',
        'Ext.data.proxy.JsonP',
        'Ext.data.reader.Json'
    ],

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            autoLoad: true,
            model: 'Clientv1.model.binTableModel',
            storeId: 'MyJsonPStorebinTable',
            proxy: {
                type: 'jsonp',
                url: 'http://localhost/apiv1/CALM/nmdadb/binTable/lastweek',
                callbackKey: 'hola',
                reader: {
                    type: 'json'
                }
            }
        }, cfg)]);
    }
});