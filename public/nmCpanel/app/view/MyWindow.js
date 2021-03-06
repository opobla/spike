/*
 * File: app/view/MyWindow.js
 *
 * This file was generated by Sencha Architect
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

Ext.define('MyApp.view.MyWindow', {
    extend: 'Ext.window.Window',

    requires: [
        'Ext.form.field.Date'
    ],

    draggable: false,
    height: 250,
    itemId: 'PruebaGrid',
    width: 400,
    collapsible: true,
    title: 'My Window',

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            items: [
                {
                    xtype: 'datefield',
                    fieldLabel: 'Label'
                }
            ]
        });

        me.callParent(arguments);
    }

});