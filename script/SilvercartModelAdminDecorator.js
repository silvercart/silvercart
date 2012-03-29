var preventAutoloadFor = [
    $PreventAutoLoadForManagedModels
];
var enableFirstEntryAutoLoadFor = [
    $EnabledFirstEntryAutoLoadForManagedModels
];

(function($) {
    $(document).ready(function() {
        var managedModelClass;
        
        if ($('#ModelClassSelector select').length > 0) {
            $('#ModelClassSelector select').change(function(){
                managedModelClass = $(this).val().replace('Form_', '');
                
                if (jQuery.inArray(managedModelClass, preventAutoloadFor) === -1) {
                    $('#' + $('.Actions input[name="action_search"]:visible').attr('id').replace('_action_search','')).triggerHandler('submit');
                    if (jQuery.inArray(managedModelClass, enableFirstEntryAutoLoadFor) >= 0) {
                        loadFirstEntry(managedModelClass);
                    }
                } else {
                    $('#Form_ResultsForm').html(' ');
                }
            });
        }
        if ($('#SearchForm_holder .tabstrip').length > 0) {
            $('#SearchForm_holder .tabstrip a').click(function(){
                var index = $(this).attr('href').indexOf('#') + 1;
                var FormID = $(this).attr('href').substring(index).replace('Form_','Form_SearchForm_');
                
                managedModelClass = FormID.replace('Form_SearchForm_', '');
                
                if (jQuery.inArray(managedModelClass, preventAutoloadFor) === -1) {
                    $('#' + FormID).triggerHandler('submit');
                    if (jQuery.inArray(managedModelClass, enableFirstEntryAutoLoadFor) >= 0) {
                        loadFirstEntry(managedModelClass);
                    }
                } else {
                    $('#Form_ResultsForm').html(' ');
                }
            });
        }
        if ($('.Actions input[name="action_search"]:visible').length > 0) {
            managedModelClass = '' + $('.Actions input[name="action_search"]:visible').attr('id').replace('_action_search','').replace('Form_SearchForm_', '');
            
            if (jQuery.inArray(managedModelClass, preventAutoloadFor) === -1) {
                var formId = $('.Actions input[name="action_search"]:visible').attr('id').replace('_action_search','');

                if ($('#' + formId)) {
                    $('#' + formId).triggerHandler('submit');
                }
                if (jQuery.inArray(managedModelClass, enableFirstEntryAutoLoadFor) >= 0) {
                    loadFirstEntry(managedModelClass);
                }
            } else {
                $('#Form_ResultsForm').html(' ');
            }
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
    $('#right input[name=action_cleanDataBase]').live('click', function(){
        return triggerCleanDataBase(0);
    });
    
    var triggerCleanDataBase = function(start) {
        $('#right input[name=action_cleanDataBase]').addClass('loading')
        var cleanDataBaseButton = $('#right input[name=action_cleanDataBase]');
        var cleanDataBaseStartIndex = $('#right input[name=cleanDataBaseStartIndex]');
        var form = $('#right form');
        var formAction = form.attr('action') + '?' + $(cleanDataBaseButton).attr('name').replace('action_', '') + '&start=' + $(cleanDataBaseStartIndex).val();

        // Post the data to save
        $.post(formAction, form.formToArray(), function(result){

            $('#right #ModelAdminPanel').html(result);

            statusMessage(ss.i18n._t('SilvercartConfig.CLEANED_DATABASE'), 'good');
            $(cleanDataBaseButton).removeClass('loading');
            
            Behaviour.apply(); // refreshes ComplexTableField
            if(window.onresize) window.onresize();
        }, 'html');

        return false;
    }
    
    var loadFirstEntry = function(managedModelClass) {
        if ($('#Form_ResultsForm_' + managedModelClass + ' table.data').length == 0) {
            setTimeout(loadFirstEntry, 500, managedModelClass);
        } else {
            var table = jQuery('#Form_ResultsForm_' + managedModelClass + ' table.data');
            var td = jQuery('tbody td:first', table);
            var a = jQuery('a', td);
            a.trigger('click');
        }
    };
})(jQuery);
