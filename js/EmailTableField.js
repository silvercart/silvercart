EmailTableField = Class.create();
EmailTableField.prototype = {
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

EmailTableField.applyTo('div.EmailTableField');