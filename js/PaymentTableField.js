PaymentTableField = Class.create();
PaymentTableField.prototype = {
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

PaymentTableField.applyTo('div.PaymentTableField');