/*
 * File: app.js
 *
 * This file was generated by Sencha Architect version 3.0.3.
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

// @require @packageOverrides
Ext.Loader.setConfig({
    enabled: true
});


Ext.application({
    IsUncorrected: true,
    IsCorrPress: false,
    IsCorrEffic: false,
    IsCandle: true,
    models: [
        'MyModel'
    ],
    stores: [
        'MyJsonPStore'
    ],
    views: [
        'MyViewport',
        'MyPanel5',
        'MyPanel6',
        'MyPanel3',
        'MyPanel4'
    ],
    controllers: [
        'Spike',
        'Navigation'
    ],
    name: 'MyApp',

    launch: function() {
        Ext.create('MyApp.view.MyViewport');
    }

});
