
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
	
    /**
     * RHS panel addExampleData and addExampleConfig button 
     */
    $('#right input[name=action_addExampleData],#right input[name=action_addExampleConfig]').live('click', function(){
        $(this).addClass('loading')
        var exampleDataButton = $(this);
        var form = $('#right form');
        var formAction = form.attr('action') + '?' + $(this).attr('name').replace('action_', '');

        // Post the data to save
        $.post(formAction, form.formToArray(), function(result){

            $('#right #ModelAdminPanel').html(result);

            if($('#right #ModelAdminPanel form').hasClass('addedExampleData')) {
                statusMessage(ss.i18n._t('SilvercartConfig.ADDED_EXAMPLE_DATA', 'Added Example Data'), 'good');
            } else if($('#right #ModelAdminPanel form').hasClass('addedExampleConfig')) {
                statusMessage(ss.i18n._t('SilvercartConfig.ADDED_EXAMPLE_CONFIGURATION', 'Added Example Configuration'), 'good');
            } else if($('#right #ModelAdminPanel form').hasClass('exampleDataAlreadyAdded')) {
                statusMessage(ss.i18n._t('SilvercartConfig.EXAMPLE_DATA_ALREADY_ADDED', 'Example Data already added'), 'bad');
            } else if($('#right #ModelAdminPanel form').hasClass('exampleConfigAlreadyAdded')) {
                statusMessage(ss.i18n._t('SilvercartConfig.EXAMPLE_CONFIGURATION_ALREADY_ADDED', 'Example Configuration already added'), 'bad');
            }
            $(exampleDataButton).removeClass('loading');
            
            Behaviour.apply(); // refreshes ComplexTableField
            if(window.onresize) window.onresize();
        }, 'html');

        return false;
    });
})(jQuery);