<div id="$id" class="$CSSClasses field">
    <% if Print %>
    <% else %>
        <% if Markable %>
            <% include TableListField_SelectOptions %>
    <% if BatchActions %>
    <div class="silvercart-batch-options">
        <label for="silvercart-batch-option-select"><% _t('SilvercartEditableTableListField.BATCH_OPTIONS_LABEL') %>:</label>
        <select id="silvercart-batch-option-select" name="silvercart-batch-option-select">
            <option value=""><% _t('SilvercartOrderSearchForm.PLEASECHOOSE') %></option>
        <% loop BatchActions %>
            <option value="$action">$label</option>
        <% end_loop %>
        </select>
        <span class="silvercart-batch-option-callback-target">
            
        </span>
        <input type="hidden" name="silvercart-batch-option-callback-data" id="silvercart-batch-option-callback-data" value="" />
        <input type="button" name="silvercart-batch-option-execute" id="silvercart-batch-option-execute" value="<% _t('Silvercart.EXECUTE') %>" />
    </div>
    <% end_if %>
        <% end_if %>
        <% include TableListField_PageControls %>
    <% end_if %>
    <table class="data">
        <thead>
            <tr>
            <% if Markable %>
                <th width="16"><% if MarkableTitle %>$MarkableTitle<% else %>&nbsp;<% end_if %></th>
            <% end_if %>
            <% if UseQuickAccess %>
                <th width="16"><% if QuickAccessTitle %>{$QuickAccessTitle}<% else %>&nbsp;<% end_if %></th>
            <% end_if %>
            <% if Print %>
                <% loop Headings %>
                <th class="$Name">$Title</th>
                <% end_loop %>
            <% else %>
            <% loop Headings %>
                <th class="$Name">
                <% if IsSortable %>
                    <span class="sortTitle"><a href="$SortLink">$Title</a></span>
                    <span class="sortLink <% if SortBy %><% else %>sortLinkHidden<% end_if %>">
                    <% if SortDirection == "desc" %>
                        <a href="$SortLink"><img src="cms/images/bullet_arrow_up.png" alt="<% _t('SORTDESC', 'Sort in descending order') %>" /></a>
                    <% else %>
                        <a href="$SortLink"><img src="cms/images/bullet_arrow_down.png" alt="<% _t('SORTASC', 'Sort in ascending order') %>" /></a>
                    <% end_if %>
                        </a>
                        &nbsp;
                    </span>
                <% else %>
                    <span>$Title</span>
                <% end_if %>
                </th>
            <% end_loop %>
            <% end_if %>
            <% if Can(delete) %><th width="18">&nbsp;</th><% end_if %>
            </tr>
        </thead>

    <% if HasSummary %>
        <tfoot>
            <tr class="summary">
                <% include TableListField_Summary %>
            </tr>
        </tfoot>
    <% end_if %>

        <tbody>
    <% if HasGroupedItems %>
        <% loop GroupedItems %>
            <% loop Items %>
                <% include SilvercartEditableTableListField_Item %>
            <% end_loop %>
            <tr class="summary partialSummary">
                <% include TableListField_Summary %>
            </tr>
        <% end_loop %>
    <% else %>
        <% if Items %>
            <% loop Items %>
                <% include SilvercartEditableTableListField_Item %>
            <% end_loop %>
        <% else %>
            <tr class="notfound">
            <% if Markable %>
                <th width="18">&nbsp;</th>
            <% end_if %>
                <td colspan="$Headings.Count"><i><% _t('NOITEMSFOUND','No items found') %></i></td>
            <% if Can(delete) %>
                <td width="18">&nbsp;</td>
            <% end_if %>
            </tr>
        <% end_if %>
        <% if Can(add) %>
            $AddRecordAsTableRow
        <% end_if %>
    <% end_if %>
        </tbody>
    </table>
    
    $ExtraData
    
</div>

<script type="text/javascript">
    /* <![CDATA[ */
    (function($) {
        $(document).ready(function() {
            if (typeof SilvercartEditableTableListFieldScriptIsLoaded == 'boolean' &&
                SilvercartEditableTableListFieldScriptIsLoaded == true) {
                return;
            }
            var SilvercartEditableTableListFieldShowLoadingBar = function(callback) {
                if ($('#silvercart-batch-option-loading-bar').length == 0) {
                    $('.silvercart-batch-options').append('<div id="silvercart-batch-option-loading-bar"></div>');
                }
                $('#silvercart-batch-option-loading-bar').fadeTo(300, 0.8, callback);
            };
            var SilvercartEditableTableListFieldHideLoadingBar = function() {
                $('#silvercart-batch-option-loading-bar').fadeOut();
            };
            $('#silvercart-batch-option-select').live('change', function() {
                SilvercartEditableTableListFieldShowLoadingBar(function() {
                    $('.silvercart-batch-option-callback-target').html('');
                    var action      = $('#silvercart-batch-option-select option:selected').val();
                    var callBack    = 'silvercartBatch_' + action;
                    if(eval('typeof ' + callBack + " == 'function'")) {
                        eval(callBack + '();')
                    }
                    SilvercartEditableTableListFieldHideLoadingBar();
                });
            });
            $('.SilvercartEditableTableListField .markingcheckbox input').live('change', function() {
                var selectedIDs = [];
                $('.markingcheckbox input:checked').each(function() {
                    selectedIDs.push($(this).val());
                });
                $('input[name="$Name[selected]"]').val(selectedIDs.join(','));
            });
            $('#silvercart-batch-option-execute').live('click', function() {
                SilvercartEditableTableListFieldShowLoadingBar();
                var selectedIDs = [];
                $('.markingcheckbox input:checked').each(function() {
                    selectedIDs.push($(this).val());
                });
                $('input[name="$Name[selected]"]').val(selectedIDs.join(','));
                if (selectedIDs.length == 0) {
                    var message = 'No objects selected! Please select at least one object entry.';
                    if(typeof(ss) != 'undefined' && typeof(ss.i18n) != 'undefined') {
                        message = ss.i18n._t('SilvercartEditableTableListField.NO_ENTRY_SELECTED', message);
                    }
                    alert(message);
                    SilvercartEditableTableListFieldHideLoadingBar();
                } else if ($('#silvercart-batch-option-select option:selected').val() == '') {
                    var message = 'No action selected! Please select an action to execute.';
                    if(typeof(ss) != 'undefined' && typeof(ss.i18n) != 'undefined') {
                        message = ss.i18n._t('SilvercartEditableTableListField.NO_ACTION_SELECTED', message);
                    }
                    alert(message);
                    SilvercartEditableTableListFieldHideLoadingBar();
                } else {
                    $.ajax({
                        type:   'POST',
                        url:    '{$ControllerLink}/doBatchAction',
                        data:   {
                            '$Name[selected]'   : $('input[name="$Name[selected]"]').val(),
                            'BatchActionToCall' : $('#silvercart-batch-option-select option:selected').val(),
                            'BatchCallbackData' : $('#silvercart-batch-option-callback-data').val()
                        },
                        success: function(data) {
                            SilvercartEditableTableListFieldHideLoadingBar();
                            eval(data);
                            $('#Form_SearchForm_$Name').submit();
                        },
                        failure: function(data) {
                            var message = 'Batch action failed!';
                            if(typeof(ss) != 'undefined' && typeof(ss.i18n) != 'undefined') {
                                message = ss.i18n._t('SilvercartEditableTableListField.BATCH_FAILED', message);
                            }
                            alert(message);
                            alert('Aktion fehlgeschlagen!');
                            SilvercartEditableTableListFieldHideLoadingBar();
                        }
                    });
                }
            });
        });
    })(jQuery);
    var SilvercartEditableTableListFieldScriptIsLoaded = true;
    /* ]]> */
</script>
