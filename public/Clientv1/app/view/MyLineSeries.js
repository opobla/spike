/*
 * File: app/view/MyLineSeries.js
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

Ext.define('Clientv1.view.MyLineSeries', {
    extend: 'Ext.chart.series.Line',
    alias: 'widget.mylineseries',

    xField: 'start_date_time',
    yField: 'ch01',
    fill: true,
    smooth: 3,

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            type: 'line'
        }, cfg)]);
    }
});