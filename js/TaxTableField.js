TaxTableField = Class.create();
TaxTableField.prototype = {
	initialize: function() {
		var rules = {};

		rules['#Form_EditForm'] = {
			changeDetection_fieldsToIgnore : {
				'Title' : true,
				'Rate' : true
			}
		}

		Behaviour.register(rules);
	}
}

TaxTableField.applyTo('div.TaxTableField');

