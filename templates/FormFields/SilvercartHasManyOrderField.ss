<% require css(silvercart/css/screen/backend/SilvercartHasManyOrder.css) %>

<input type="hidden" id="{$ID}_relationName" name="relationName" value="$relationName" />

<div class="silvercart-hasmanyorder-field">
    <div class="subcolumns">
        <div class="c45l">
            <p>
                <% _t('SilvercartHasManyOrderField.AVAILABLE_RELATION_OBJECTS') %>:
            </p>
            <select id="{$ID}_available_items" class="silvercart-hasmanyorder-field-available-items" name="availableItems[]" multiple="multiple" size="20">
                <% control available_items %>
                    <option value="$value">$label</option>
                <% end_control %>
            </select>
        </div>
        <div class="c10l">
            <p>&nbsp;</p>
            <input type="submit" id="{$ID}_action_doAttributeItems" class="action_doAttributeItems" name="action_doAttributeItems" value="" class="silvercart-hasmanyorder-field-middle-button action_doAttributeItems" /><br />
            <input type="submit" id="{$ID}_action_doRemoveItems" class="action_doRemoveItems" name="action_doRemoveItems" value="" class="silvercart-hasmanyorder-field-middle-button action_doRemoveItems" />
        </div>
        <div class="c45r">
            <p>
                <% _t('SilvercartHasManyOrderField.ATTRIBUTED_FIELDS') %>:
            </p>
            <select id="{$ID}_selected_items" class="silvercart-hasmanyorder-field-selected-items" name="selectedItems[]" multiple="multiple" size="20">
                <% control selected_items %>
                    <option value="$value">$label</option>
                <% end_control %>
            </select>
            
            <div class="silvercart-hasmanyorder-field-action-row">
                <input id="{$ID}_action_doMoveUpItems" class="action_doMoveUpItems" type="submit" name="action_doMoveUpItems" title="<% _t('SilvercartHasManyOrderField.MOVE_UP') %>" value="" />
                <input id="{$ID}_action_doMoveDownItems" class="action_doMoveDownItems" type="submit" name="action_doMoveDownItems" title="<% _t('SilvercartHasManyOrderField.MOVE_DOWN') %>" value="" />
                <input id="{$ID}_action_editItem" class="action_doEditItem" type="submit" name="action_doEditItem" title="<% _t('SilvercartHasManyOrderField.EDIT') %>" value="" />
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    /* <![CDATA[ */
    // Add functionality to the move up, down, remove and attribute items buttons
    (function($) {
        function SilvercartHasManyOrderFieldApply() {
            var form_actions_right_html = '';
            if ($('#form_actions_right').length > 0) {
                form_actions_right_html = $('#form_actions_right').html();
            }
            Behaviour.apply();
            if (window.onresize) {
                window.onresize();
            }
            if ($('#form_actions_right').length == 0 &&
                form_actions_right_html != '') {
                $('#right').append('<div id="form_actions_right" class="ajaxActions">' + form_actions_right_html + '</div>');
            }
        }
        
        $('#right input[name=action_doAttributeItems], #right input[name=action_doRemoveItems], #right input[name=action_doMoveUpItems], #right input[name=action_doMoveDownItems]').live('click', function() {
            $(this).addClass('loading')
            var SilvercartHasManyOrderFieldButtonAttributeItems = $(this);
            var form       = $('#right form');
            var formAction = form.attr('action') + '?' + $(this).attr('name').replace('action_', '');

            // Post the data to save
            $.post(formAction, form.formToArray(), function(result) {
                $('#right #ModelAdminPanel').html(result);

                $(SilvercartHasManyOrderFieldButtonAttributeItems).removeClass('loading');

                Behaviour.apply(); // refreshes ComplexTableField
                if (window.onresize) {
                    window.onresize();
                }
                if ($('#Form_EditForm .tabstrip:first #tab-' + $('#{$ID}_relationName').parent('.tab').attr('id')).length > 0) {
                    $('#Form_EditForm .tabstrip:first #tab-' + $('#{$ID}_relationName').parent('.tab').attr('id')).trigger('click');
                }
            }, 'html');

            return false;
        });
        // The Edit button functionality
        $('#right input[name=action_doEditItem]').live('click', function() {
            var editItemID      = ($('#{$ID}_selected_items option:selected').val());
            var animationSpeed  = 300;

            if ($('#SilvercartOverlay').length == 0) {
                $('body').append('<div id="SilvercartOverlay"></div>');
            }
            if ($('#SilvercartWidgetEditForm').length == 0) {
                $('body').append('<div id="SilvercartWidgetEditForm"><div id="SilvercartWidgetEditForm_content"><div id="SilvercartWidgetEditForm_loader"></div><div id="SilvercartWidgetEditForm_Form" class="right"></div><div id="SilvercartWidgetEditForm_Controls"><a href="#">Close</a></div></div></div>');
                $('#SilvercartWidgetEditForm').css({
                    display : 'none'
                });
            }
            $('#SilvercartOverlay').fadeIn(animationSpeed, function() {
                $('#SilvercartWidgetEditForm_loader').show();
                $('#SilvercartWidgetEditForm').fadeIn(animationSpeed);
            });

            // Load the edit form data and put it into an overlay container
            $.post('{$AbsUrl}admin/silvercart-widgets/SilvercartWidget/' + editItemID + '/edit', new Array(), function(result) {
                $('#SilvercartWidgetEditForm_Form').html(result);
                SilvercartHasManyOrderFieldApply();
                if ($('#SilvercartWidgetEditForm_Form .tabstrip:first .current').length == 0 &&
                    $('#SilvercartWidgetEditForm_Form .tabstrip:first .first').length > 0) {
                    $('#SilvercartWidgetEditForm_Form .tabstrip:first .first a').trigger('click');
                    if (typeof RelationComplexTableField != 'undefined') {
                        RelationComplexTableField.applyTo('#SilvercartWidgetEditForm_Form div.ManyManyComplexTableField');
                    }
                }
                $('#SilvercartWidgetEditForm_loader').fadeOut(animationSpeed);
                
                // Add functionality to the Save button
                $('#SilvercartWidgetEditForm_Form input[name=action_doSave]').live('click', function() {
                    var form = $('#SilvercartWidgetEditForm_Form form');
                    var formAction = form.attr('action') + '?' + $(this).fieldSerialize() + '&action_doSave=Save';
                    
                    $.post(formAction, form.formToArray(), function(result){
                        $('#SilvercartWidgetEditForm').fadeOut(animationSpeed, function() {
                            $('#SilvercartOverlay').fadeOut(animationSpeed, function() {
                                $('#SilvercartWidgetEditForm_Form').html('');
                            });
                        });
                        
                        return false;
                    }, 'html');
                    
                    return false;
                });
                
                // Remove other action buttons
                $('#SilvercartWidgetEditForm_Form input[name=action_doDelete]').css('display', 'none');
                $('#SilvercartWidgetEditForm_Form input[name=action_goBack]').css('display', 'none');
                
                // Close button
                $('#SilvercartWidgetEditForm_Controls a').live('click', function() {
                    $('#SilvercartWidgetEditForm').fadeOut(animationSpeed, function() {
                        $('#SilvercartOverlay').fadeOut(animationSpeed, function() {
                            if ($('#Form_EditForm .tabstrip:first #tab-' + $('#{$ID}_relationName').parent('.tab').attr('id')).length > 0) {
                                $('#Form_EditForm .tabstrip:first #tab-' + $('#{$ID}_relationName').parent('.tab').attr('id')).trigger('click');
                            }
                        });
                    });
                    return false;
                });
                
            }, 'html');

            return false;
        });
    })(jQuery);
    /* ]]> */
</script>
