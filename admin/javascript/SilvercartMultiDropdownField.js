
(function($) {
    $(document).ready(function() {
        $('.silvercartmultidropdown select').live('change', function() {
            var booleanNo   = 'No';
            var booleanYes  = 'Yes';
            if (typeof(ss) != 'undefined' && typeof(ss.i18n) != 'undefined') {
                booleanNo   = ss.i18n._t('Boolean.NO', 'No');
                booleanYes  = ss.i18n._t('Boolean.YES', 'Yes');
            }
            var selectFieldID   = $(this).attr('id');
            var selectFieldName = $(this).attr('name').replace(/-orig/, '');
            if ($('#' + selectFieldID + '-selection-' + $(this).val()).length !== 0) {
                return;
            }
            if ($('#' + selectFieldID + '-selection').length === 0) {
                $('#' + selectFieldID + '.silvercartmultidropdown').closest('.middleColumn').append('<div id="' + selectFieldID + '-selection" class="silvercartmultidropdown-selection"></div>');
            }
            var selectionMarkup = '';
            selectionMarkup += '<div id="' + selectFieldID + '-selection-' + $(this).val() + '" class="silvercartmultidropdown-selection-entry">';
                selectionMarkup += '<input id="' + selectFieldID + '-selection-value-' + $(this).val() + '" type="hidden" name="' + selectFieldID + '-selection[]" value="' + $(this).val() + '" />';
                selectionMarkup += '<span class="silvercartmultidropdown-selection-left">';
                    selectionMarkup += $('option:selected', $(this)).text();
                selectionMarkup += '</span>';
                selectionMarkup += '<span class="silvercartmultidropdown-selection-right">';
                    selectionMarkup += '<input id="' + selectFieldID + '-selection-yes-' + $(this).val() + '" type="radio" name="' + selectFieldName + '[' + $(this).val() + ']" value="1" checked="checked" />';
                    selectionMarkup += '<label for="' + selectFieldID + '-selection-yes-' + $(this).val() + '">' + booleanYes + '</label>';
                    selectionMarkup += '<input id="' + selectFieldID + '-selection-no-' + $(this).val() + '" type="radio" name="' + selectFieldName + '[' + $(this).val() + ']" value="0" />';
                    selectionMarkup += '<label for="' + selectFieldID + '-selection-no-' + $(this).val() + '">' + booleanNo + '</label>';
                    selectionMarkup += '<a href="javascript:;" rel="' + $(this).val() + '" role="' + selectFieldID + '" class="silvercartmultidropdown-remove-selection"><img src="/cms/images/delete-small.gif" alt="X" /></a>';
                selectionMarkup += '</span>';
            selectionMarkup += '</div>';
            $('#' + selectFieldID + '-selection').append(selectionMarkup);
            $('option[value="' + $(this).val() + '"]', $(this)).remove();
            
            var chzn_width = parseInt($('#' + selectFieldID + '_chzn').css('width'));
            $('#' + selectFieldID + '_chzn').remove();
            $(this).removeClass('chzn-done').chosen({
                allow_single_deselect: true,
                disable_search_threshold: 20,
                width: chzn_width
            });
        });
        $('.silvercartmultidropdown-remove-selection').live('click', function(event) {
            event.preventDefault();
            var selectFieldID       = $(this).attr('role');
            var selectFieldValue    = $(this).attr('rel');
            $('#' + selectFieldID + '-selection-' + selectFieldValue).slideUp('slow', function() {
                var optionLabel = $('.silvercartmultidropdown-selection-left', $(this)).text();
                $("<option/>").val(selectFieldValue).text(optionLabel).appendTo('select#' + selectFieldID);
                
                var sorted = $.makeArray($('select#' + selectFieldID + ' option')).sort(function(a,b){
                    return $(a).text() > $(b).text() ? 1:-1;
                });
                
                $('select#' + selectFieldID).empty().append(sorted);
                
                var emptyOptionLabel = $('select#' + selectFieldID + ' option[value=""]').text();
                $('select#' + selectFieldID + ' option[value=""]').remove();
                $("<option/>").val('').text(emptyOptionLabel).prependTo('select#' + selectFieldID);
                $('select#' + selectFieldID + ' option[value=""]').attr('selected', true);
                
                $(this).remove();
                var chzn_width = parseInt($('#' + selectFieldID + '_chzn').css('width'));
                $('#' + selectFieldID + '_chzn').remove();
                $('select#' + selectFieldID).removeClass('chzn-done').chosen({
                    allow_single_deselect: true,
                    disable_search_threshold: 20,
                width: chzn_width
                });
            });
        });
    });
})(jQuery);