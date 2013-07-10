
var SilvercartManyManyTextAutoCompleteField = [];
    SilvercartManyManyTextAutoCompleteField.AutoCompleteList = [];
    SilvercartManyManyTextAutoCompleteField.EntryDelimiter   = [];

SilvercartManyManyTextAutoCompleteField.split = function(val, fieldName) {
    return val.split(SilvercartManyManyTextAutoCompleteField.EntryDelimiter[fieldName]);
};
SilvercartManyManyTextAutoCompleteField.extractLast = function(term, fieldName) {
    return this.split(term, fieldName).pop();
};
SilvercartManyManyTextAutoCompleteField.init = function() {
    jQuery('.silvercartmanymanytextautocomplete input').live(
        "focus",
        function() {
            var fieldName     = jQuery(this).attr('name');
            var availableTags = SilvercartManyManyTextAutoCompleteField.AutoCompleteList[fieldName];
            jQuery(this).autocomplete({
                minLength: 3,
                source: function(request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response(jQuery.ui.autocomplete.filter(
                        availableTags, SilvercartManyManyTextAutoCompleteField.extractLast(request.term, fieldName)));
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = SilvercartManyManyTextAutoCompleteField.split(this.value, fieldName);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(SilvercartManyManyTextAutoCompleteField.EntryDelimiter[fieldName]);
                    return false;
                }
            });
    });
    jQuery('.silvercartmanymanytextautocomplete input').live(
        "keydown",
        function(event) {
            if (event.keyCode === jQuery.ui.keyCode.TAB &&
                jQuery(this).data('autocomplete').menu.active) {
                event.preventDefault();
            }
    });
};

jQuery;
(function($) {
    $(document).ready(function() {
        setTimeout('SilvercartManyManyTextAutoCompleteField.init();', 1000);
    });
})(jQuery);