<% require css(silvercart/css/screen/backend/SilvercartManyManyOrder.css) %>

<input type="hidden" id="{$ID}_relationName" name="relationName" value="$relationName" />
<input type="hidden" id="{$ID}_manyManyClass" name="manyManyClass" value="$manyManyClass" />
<input type="hidden" id="{$ID}_belongsManyManyClass" name="belongsManyManyClass" value="$belongsManyManyClass" />

<div class="silvercart-manymanyorder-field">
    <div class="subcolumns">
        <div class="c45l">
            <p>
                <% _t('SilvercartManyManyOrderField.AVAILABLE_RELATION_OBJECTS') %>:
            </p>
            <select id="{$ID}_available_items" class="silvercart-manymanyorder-field-available-items" name="availableItems[]" multiple="multiple" size="20">
                <% control available_items %>
                    <option value="$value">$label</option>
                <% end_control %>
            </select>
        </div>
        <div class="c10l">
            <p>&nbsp;</p>
            <input type="submit" id="{$ID}_action_doAttributeItems" class="action_doAttributeItems" name="action_doAttributeItems" value="" class="silvercart-manymanyorder-field-middle-button action_doAttributeItems" /><br />
            <input type="submit" id="{$ID}_action_doRemoveItems" class="action_doRemoveItems" name="action_doRemoveItems" value="" class="silvercart-manymanyorder-field-middle-button action_doRemoveItems" />
        </div>
        <div class="c45r">
            <p>
                <% _t('SilvercartManyManyOrderField.ATTRIBUTED_FIELDS') %>:
            </p>
            <select id="{$ID}_selected_items" class="silvercart-manymanyorder-field-selected-items" name="selectedItems[]" multiple="multiple" size="20">
                <% control selected_items %>
                    <option value="$value">$label</option>
                <% end_control %>
            </select>
            
            <div class="silvercart-manymanyorder-field-action-row">
                <input id="{$ID}_action_doMoveUpItems" class="action_doMoveUpItems" type="submit" name="action_doMoveUpItems" title="<% _t('SilvercartManyManyOrderField.MOVE_UP') %>" value="" />
                <input id="{$ID}_action_doMoveDownItems" class="action_doMoveDownItems" type="submit" name="action_doMoveDownItems" title="<% _t('SilvercartManyManyOrderField.MOVE_DOWN') %>" value="" />
                <input id="{$ID}_action_editItem" class="action_doEditItem" type="submit" name="action_doEditItem" title="<% _t('SilvercartManyManyOrderField.EDIT') %>" value="" />
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    /* <![CDATA[ */
    
    // Add functionality to the move up, down, remove and attribute items buttons
    (function($) {
        $('body').append('<div id="SilvercartOverlay"></div>');
        
        $('#right input[name=action_doAttributeItems], #right input[name=action_doRemoveItems], #right input[name=action_doMoveUpItems], #right input[name=action_doMoveDownItems]').live('click', function() {
            $(this).addClass('loading')
            var SilvercartManyManyOrderFieldButtonAttributeItems = $(this);
            var form       = $('#right form');
            var formAction = form.attr('action') + '?' + $(this).attr('name').replace('action_', '');

            // Post the data to save
            $.post(formAction, form.formToArray(), function(result) {
                $('#right #ModelAdminPanel').html(result);

                $(SilvercartManyManyOrderFieldButtonAttributeItems).removeClass('loading');

                Behaviour.apply(); // refreshes ComplexTableField
                if(window.onresize) window.onresize();
            }, 'html');

            return false;
        });
    })(jQuery);
    
    // The Edit button functionality
    (function($) {
        $('#right input[name=action_doEditItem]').live('click', function() {
            var editItemID = ($('#{$ID}_selected_items option:selected').val());

            // Load the edit form data and put it into an overlay container
            $.post('{$AbsUrl}{$getRelationEditLink}' + editItemID + '/edit', new Array(), function(result) {
                $('#SilvercartOverlay').css('display', 'block');
                $('body').append('<div id="SilvercartManyManyOrderFieldEditForm"><div id="SilvercartManyManyOrderFieldEditForm_content"><div id="SilvercartManyManyOrderFieldEditForm_Form" class="right"></div><div id="SilvercartManyManyOrderFieldEditForm_Controls"><a href="#">Close</a></div></div></div>');
                $('#SilvercartManyManyOrderFieldEditForm_Form').html(result);
                
                // Add functionality to the Save button
                $('#SilvercartManyManyOrderFieldEditForm_Form input[name=action_doSave]').live('click', function() {
                    $('#SilvercartOverlay').css('display', 'none');
                    var form = $('#SilvercartManyManyOrderFieldEditForm_Form form');
                    var formAction = form.attr('action') + '?' + $(this).fieldSerialize() + '&action_doSave=Save';
                    
                    $.post(formAction, form.formToArray(), function(result){
                        $('#SilvercartManyManyOrderFieldEditForm').remove();
                        
                        return false;
                    }, 'html');
                    
                    return false;
                });
                
                // Remove other action buttons
                $('#SilvercartManyManyOrderFieldEditForm_Form input[name=action_doDelete]').css('display', 'none');
                $('#SilvercartManyManyOrderFieldEditForm_Form input[name=action_goBack]').css('display', 'none');
                
                // Close button
                $('#SilvercartManyManyOrderFieldEditForm_Controls a').live('click', function() {
                    $('#SilvercartOverlay').css('display', 'none');
                    $('#SilvercartManyManyOrderFieldEditForm').remove();

                    return false;
                });
                
                Behaviour.apply('SilvercartManyManyOrderFieldEditForm_Form form');
                openTab('{$getRelationEditTabToOpen}');
            }, 'html');

            return false;
        });
    })(jQuery);
    /* ]]> */
</script>

