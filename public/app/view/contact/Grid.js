var sm = Ext.create('Ext.selection.CheckboxModel');
Ext.define('Alegra.view.contact.Grid', {
	extend: 'Ext.grid.Panel',
	alias: 'widget.contactGrid',
	requires: ['Ext.toolbar.Paging'],
	iconCls: 'x-fa fa-user',
	title: 'Contactos',
	selModel: sm,
	store: 'Contacts',
	stripeRows: true,
	columnLines: true,
	height: '90%',
	id: 'contactGrid',
	columns: [{
			header: 'Nombre',
			width: 200,
			flex: 1,
			dataIndex: 'name',
			align: 'left',
			menuDisabled: true,
		}, {
			header: 'Identificación',
			width: 100,
			flex: 1,
			dataIndex: 'identification',
			align: 'left',
			menuDisabled: true,
		}, {
			header: 'Teléfono 1',
			width: 100,
			flex: 1,
			dataIndex: 'phonePrimary',
			align: 'left',
			menuDisabled: true,
		}, {
			header: 'Observaciones',
			width: 150,
			flex: 1,
			dataIndex: 'observations',
			align: 'left',
			menuDisabled: true,
		}, {
			xtype: 'actioncolumn',
			width: 100,
			text: 'Acciones',
			align: 'center',
			flex: 1,
			menuDisabled: true,
			items: [
				{
					iconCls: 'x-fa fa-eye',
					handler: function (grid, rowIndex, colIndex, item, e, record, row) {
						var rec = grid.getStore().getAt(rowIndex);
						var formDetail = Ext.create('Alegra.view.contact.Detail').show();
						formDetail.down('form').loadRecord(rec);
					},
				}, {
					iconCls: 'x-fa fa-pencil',
					handler: function (grid, rowIndex, colIndex) {
						var rec = grid.getStore().getAt(rowIndex);
						var formEdit = Ext.create('Alegra.view.contact.Form').show();
						if (rec.store !== null) {
							
							formEdit.down('form').loadRecord(rec);
						}
					},
				}, {
					iconCls: 'x-fa fa-trash-o',
					handler: function (grid, rowIndex, colIndex) {
						var rec = grid.getStore().getAt(rowIndex);
						var store = grid.getStore();
						Ext.Msg.show({
							title: 'Eliminar contacto',
							msg: '¿Estás seguro de que deseas eliminar el contacto? Esta operación no se puede deshacer',
							buttons: Ext.Msg.YESNOCANCEL,
							icon: Ext.MessageBox.QUESTION,
							scope: this,
							width: 600,
							fn: function (btn) {
								if (btn === 'yes') {
									store.remove(rec);
									store.sync({
										success: function (batch, action) {
											store.load();
											var reader = batch.proxy.getReader();
											Ext.Msg.alert('Success', reader.jsonData.message);
										},
										failure: function (batch, action) {
											var reader = batch.proxy.getReader();
											Ext.Msg.alert('Failed', reader.jsonData ? 
															reader.jsonData.message : 'No response');
										},
										scope: this,
									});
								}
							}
						});
					},
				},
			],
		}],
	initComponent: function () {
		this.dockedItems = [{
				xtype: 'toolbar',
				items: [{
						iconCls: 'x-fa fa-plus-circle',
						text: 'Nuevo Contacto',
						action: 'add',
					}, {
						iconCls: 'x-fa fa-trash-o',
						text: 'Eliminar',
						action: 'delete',
					}],
			}, {
				xtype: 'pagingtoolbar',
				dock: 'bottom',
				store: 'Contacts',
				displayInfo: true,
				displayMsg: 'Mostrando Contactos {0} - {1} de {2}',
				emptyMsg: "Ning\u00FAn contacto encontrado.",
			}];
		this.callParent(arguments);
	}
});
