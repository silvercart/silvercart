
(function($) {
    $(document).ready(function() {
        if ($('#ModelClassSelector select').length > 0) {
            $('#ModelClassSelector select').change(function(){
                $('#' + $('.Actions input[name="action_search"]:visible').attr('id').replace('_action_search','')).triggerHandler('submit');
            });
        }
        if ($('#SearchForm_holder .tabstrip').length > 0) {
            $('#SearchForm_holder .tabstrip a').click(function(){
                var index = $(this).attr('href').indexOf('#') + 1;
                var FormID = $(this).attr('href').substring(index).replace('Form_','Form_SearchForm_');
                $('#' + FormID).triggerHandler('submit');
            });
        }
        if ($('.Actions input[name="action_search"]:visible').length > 0) {
            $('#' + $('.Actions input[name="action_search"]:visible').attr('id').replace('_action_search','')).triggerHandler('submit');
        }
    });
})(jQuery);