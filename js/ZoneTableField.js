ZoneTableField = Class.create();
ZoneTableField.prototype = {
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

ZoneTableField.applyTo('div.ZoneTableField');