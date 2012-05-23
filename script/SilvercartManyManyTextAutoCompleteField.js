
var SilvercartManyManyTextAutoCompleteField = [];
    SilvercartManyManyTextAutoCompleteField.AutoCompleteList = [];

SilvercartManyManyTextAutoCompleteField.split = function(val) {
    return val.split(/,\s*/);
}
SilvercartManyManyTextAutoCompleteField.extractLast = function(term) {
    return this.split(term).pop();
}
SilvercartManyManyTextAutoCompleteField.init = function() {
    jQuery('.silvercartmanymanytextautocomplete input').live(
        "focus",
        function() {
            var availableTags = SilvercartManyManyTextAutoCompleteField.AutoCompleteList[jQuery(this).attr('name')];
            jQuery(this).autocomplete({
                minLength: 0,
                source: function(request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response(jQuery.ui.autocomplete.filter(
                        availableTags, SilvercartManyManyTextAutoCompleteField.extractLast(request.term)));
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = SilvercartManyManyTextAutoCompleteField.split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            })
    })
    jQuery('.silvercartmanymanytextautocomplete input').live(
        "keydown",
        function(event) {
            if (event.keyCode === jQuery.ui.keyCode.TAB &&
                jQuery(this).data('autocomplete').menu.active) {
                event.preventDefault();
            }
    });
}

(function($) {
    $(document).ready(function() {
        setTimeout('SilvercartManyManyTextAutoCompleteField.init();', 1000);
    });
})(jQuery);