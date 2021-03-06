/*
 * File: app/view/MyViewport1.js
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

Ext.define('Clientv1.view.MyViewport1', {
    extend: 'Ext.container.Viewport',

    requires: [
        'Ext.panel.Panel',
        'Ext.form.field.Date',
        'Ext.form.field.Number'
    ],

    layout: {
        type: 'vbox',
        align: 'stretch'
    },

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            items: [
                {
                    xtype: 'panel',
                    flex: 1,
                    title: '',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'panel',
                            title: '',
                            items: [
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Date From:'
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Date To'
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            margin: '0 0 0 10',
                            title: '',
                            items: [
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Interval:'
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    flex: 1,
                    title: '',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'panel',
                            flex: 1,
                            title: ''
                        },
                        {
                            xtype: 'panel',
                            flex: 1,
                            title: ''
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    flex: 1,
                    title: '',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'panel',
                            flex: 1,
                            title: ''
                        },
                        {
                            xtype: 'panel',
                            flex: 1,
                            title: ''
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }

});