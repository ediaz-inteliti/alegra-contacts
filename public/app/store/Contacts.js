Ext.define('Alegra.store.Contacts', {
	extend: 'Ext.data.Store',
	model: 'Alegra.model.Contact',
	autoLoad: true,
	leadingBufferZone: 10,
	pageSize: 20,
	autoLoad: {start: 0, limit: 20},
	proxy: {
		type: 'ajax',
		api: {
			create: 'contacts/create',
			read: 'contacts/index',
			update: 'contacts/update',
			destroy: 'contacts/delete'
		},
		actionMethods: {
			create: 'POST',
			read: 'GET',
			update: 'POST',
			destroy: 'POST'
		},
		reader: {
			type: 'json',
			rootProperty: 'data',
			successProperty: 'success'
		},
		writer: {
			type: 'json',
			writeAllFields: true,
			encode: true,
			rootProperty: 'data'
		}
	}
});
