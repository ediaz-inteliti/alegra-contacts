Ext.define('Alegra.view.Viewport', {
	extend: 'Ext.Viewport',
	layout: 'center',
	requires: [
		'Alegra.view.contact.Grid',
		'Alegra.view.contact.Form',
		'Alegra.view.contact.Detail',
	],
	initComponent: function () {
		Ext.apply(this, {
			items: [{
					xtype: 'contactGrid',
					width: '75%',
				}]
		});
		this.callParent();
	}
});
