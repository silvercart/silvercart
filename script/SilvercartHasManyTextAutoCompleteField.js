
var SilvercartHasManyTextAutoCompleteField = [];
    SilvercartHasManyTextAutoCompleteField.AutoCompleteList = [];
    SilvercartHasManyTextAutoCompleteField.EntryDelimiter   = [];

SilvercartHasManyTextAutoCompleteField.split = function(val, fieldName) {
    return val.split(SilvercartHasManyTextAutoCompleteField.EntryDelimiter[fieldName]);
}
SilvercartHasManyTextAutoCompleteField.extractLast = function(term, fieldName) {
    return this.split(term, fieldName).pop();
}
SilvercartHasManyTextAutoCompleteField.init = function() {
    jQuery('.silvercarthasmanytextautocomplete input').live(
        "focus",
        function() {
            var fieldName     = jQuery(this).attr('name');
            var availableTags = SilvercartHasManyTextAutoCompleteField.AutoCompleteList[fieldName];
            jQuery(this).autocomplete({
                minLength: 0,
                source: function(request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response(jQuery.ui.autocomplete.filter(
                        availableTags, SilvercartHasManyTextAutoCompleteField.extractLast(request.term, fieldName)));
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = SilvercartHasManyTextAutoCompleteField.split(this.value, fieldName);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(SilvercartHasManyTextAutoCompleteField.EntryDelimiter[fieldName]);
                    return false;
                }
            })
    })
    jQuery('.silvercarthasmanytextautocomplete input').live(
        "keydown",
        function(event) {
            if (event.keyCode === jQuery.ui.keyCode.TAB &&
                jQuery(this).data('autocomplete').menu.active) {
                event.preventDefault();
            }
    });
}

jQuery;
(function($) {
    $(document).ready(function() {
        setTimeout('SilvercartHasManyTextAutoCompleteField.init();', 1000);
    });
})(jQuery);