Ext.define('Alegra.view.contact.Detail', {
	extend: 'Ext.window.Window',
	alias: 'widget.contactDetail',
	requires: ['Ext.form.Panel', 'Ext.form.FieldSet'],
	title: 'Detalle',
	layout: 'fit',
	autoShow: true,
	width: 400,
	iconCls: 'x-fa fa-user',
	itemId: 'modalShow',
	initComponent: function () {
		this.items = [{
				xtype: 'form',
				padding: '10 10 10 10',
				border: false,
				fieldDefaults: {
					anchor: '100%',
					labelAlign: 'right',
				},
				items: [{
						xtype: 'displayfield',
						name: 'name',
						fieldLabel: 'Nombre'
					}, {
						xtype: 'displayfield',
						name: 'identification',
						fieldLabel: 'Identificación'
					}, {
						xtype: 'displayfield',
						name: 'phonePrimary',
						fieldLabel: 'Teléfono 1'
					}, {
						xtype: 'displayfield',
						name: 'phoneSecondary',
						fieldLabel: 'Teléfono 2'
					}, {
						xtype: 'displayfield',
						name: 'mobile',
						fieldLabel: 'Celular'
					}, {
						xtype: 'displayfield',
						name: 'address',
						fieldLabel: 'Dirección'
					}, {
						xtype: 'displayfield',
						name: 'city',
						fieldLabel: 'Ciudad'
					}, {
						xtype: 'displayfield',
						vtype: 'email',
						name: 'email',
						fieldLabel: 'Email'
					}, {
						xtype: 'displayfield',
						name: 'observations',
						fieldLabel: 'Observaciones'
					}]
			}];
		this.dockedItems = [{
				xtype: 'toolbar',
				dock: 'bottom',
				id: 'buttons',
				ui: 'footer',
				items: ['->', {
						iconCls: 'x-fa fa-close',
						text: 'Cerrar',
						scope: this,
						handler: this.close
					}]
			}];
		this.callParent(arguments);
	}
});
