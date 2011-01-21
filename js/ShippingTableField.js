ShippingTableField = Class.create();
ShippingTableField.prototype = {
	initialize: function() {
		var rules = {};

		rules['#Form_EditForm'] = {
			changeDetection_fieldsToIgnore : {
				'Name' : true,
				'Aktiviert' : true
			}
		}

		Behaviour.register(rules);
	}
}

ShippingTableField.applyTo('div.ShippingTableField');