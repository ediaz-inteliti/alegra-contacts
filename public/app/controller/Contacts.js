Ext.define('Alegra.controller.Contacts', {
	extend: 'Ext.app.Controller',
	stores: ['Contacts'],
	models: ['Contact'],
	views: ['contact.Form', 'contact.Grid', 'contact.Detail'],
	refs: [{
			ref: 'contactPanel',
			selector: 'panel',
		}, {
			ref: 'contactGrid',
			selector: 'grid',
		}],
	init: function () {
		this.control({
			'contactGrid dataview': {
				itemdblclick: this.editContact,
			},
			'contactGrid button[action=add]': {
				click: this.editContact,
			},
			'contactGrid button[action=delete]': {
				click: this.destroyContact,
			},
			'contactForm button[action=save]': {
				click: this.createOrUpdateContact,
			},
		});
	},
	editContact: function (grid, record) {
		var editar = Ext.create('Alegra.view.contact.Form').show();
		if (record.stores !== null) {
			editar.down('form').loadRecord(record);
		}
	},
	createOrUpdateContact: function (button) {
		var win = button.up('window');
		var form = win.down('form');
		var record = form.getRecord();
		var values = form.getValues();
		var add = false;
		var msg = 'Contacto actualizado exitosamente';

		if (values.id > 0) {
			record.set(values);
		} else {
			record = Ext.create('Alegra.model.Contact');
			record.set(values);
			this.getContactsStore().add(record);
			add = true;
			msg = 'Contacto creado exitosamente';
		}

		this.getContactsStore().sync({
			success: function (batch, action) {
				if (add) {
					this.getContactsStore().load();
				}
				Ext.Msg.alert('Success', msg);
				win.close();
			},
			failure: function (batch, action) {
				var reader = batch.proxy.getReader();
				Ext.Msg.alert('Failed', reader.jsonData ? reader.jsonData.message : 'No response');
			},
			scope: this
		});
	},
	destroyContact: function () {
		var grid = this.getContactGrid();
		var records = grid.getSelectionModel().getSelection();
		var store = this.getContactsStore();
		var title = records.length > 1 ? 'Eliminar ' + records.length + ' contactos' : 'Eliminar contacto';
		var msg = records.length > 1 ? '¿Estás seguro de eliminar ' + records.length + ' contactos? Esta operación no se puede deshacer.' : '¿Estás seguro de eliminar el contacto? Esta operación no se puede deshacer';

		if (records.length > 0) {
			Ext.Msg.show({
				title: title,
				msg: msg,
				buttons: Ext.Msg.YESNOCANCEL,
				icon: Ext.MessageBox.QUESTION,
				scope: this,
				width: 600,
				fn: function (btn) {
					if (btn === 'yes') {
						store.remove(records);
						this.getContactsStore().sync({
							success: function (batch, action) {
								this.getContactsStore().load();
								var reader = batch.proxy.getReader();
								Ext.Msg.alert('Success', reader.jsonData.message);
							},
							failure: function (batch, action) {
								var reader = batch.proxy.getReader();
								Ext.Msg.alert('Failed', reader.jsonData ? reader.jsonData.message : 'No response');
							},
							scope: this
						});
					}
				},
			});
		}
	},
});
