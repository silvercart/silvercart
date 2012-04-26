<% require css(silvercart/css/screen/backend/SilvercartMultiSelectAndOrder.css) %>

<div class="silvercart-multiselectandorder-field">
    <div class="subcolumns">
        <div class="c45l">
            <p>
                <% _t('SilvercartMultiSelectAndOrderField.NOT_ATTRIBUTED_FIELDS') %>:
            </p>
            <select id="{$ID}_available_items" class="silvercart-multiselectandorder-field-available-items" name="availableItems[]" multiple="multiple" size="20">
                <% control available_items %>
                    <option value="$value">$label</option>
                <% end_control %>
            </select>
            <br />
            <p>
                <% _t('SilvercartMultiSelectAndOrderField.ADD_CALLBACK_FIELD') %>:
            </p>
            <div id="callbackField" class="field text ">
                <label class="left" for="{$ID}_callbackField"><% _t('SilvercartMultiSelectAndOrderField.FIELD_NAME') %>:</label>
                <div class="middleColumn">
                    <input type="text" class="text" id="{$ID}_callbackField" name="callbackField" />
                </div>
            </div>
            <input id="{$ID}_action_doAddCallbackField" class="action_doAddCallbackField" type="submit" name="action_doAddCallbackField" value="<% _t('SilvercartMultiSelectAndOrderField.ADD_CALLBACK_FIELD') %>" />
        </div>
        <div class="c10l">
            <p>&nbsp;</p>
            <input type="submit" id="{$ID}_action_doAttributeItems" name="action_doAttributeItems" value="" class="silvercart-multiselectandorder-field-middle-button action_doAttributeItems" /><br />
            <input type="submit" id="{$ID}_action_doRemoveItems" name="action_doRemoveItems" value="" class="silvercart-multiselectandorder-field-middle-button action_doRemoveItems" />
        </div>
        <div class="c45r">
            <p>
                <% _t('SilvercartMultiSelectAndOrderField.ATTRIBUTED_FIELDS') %>:
            </p>
            <select id="{$ID}_selected_items" class="silvercart-multiselectandorder-field-selected-items" name="selectedItems[]" multiple="multiple" size="20">
                <% control selected_items %>
                    <option value="$value">$label</option>
                <% end_control %>
            </select>
            
            <div class="silvercart-multiselectandorder-field-action-row">
                <input id="{$ID}_action_doMoveUpItems" class="action_doMoveUpItems" type="submit" name="action_doMoveUpItems" title="<% _t('SilvercartMultiSelectAndOrderField.MOVE_UP') %>" value="" />
                <input id="{$ID}_action_doMoveDownItems" class="action_doMoveDownItems" type="submit" name="action_doMoveDownItems" title="<% _t('SilvercartMultiSelectAndOrderField.MOVE_DOWN') %>" value="" />
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    /* <![CDATA[ */
    (function($) {
        $('#right input[name=action_doAttributeItems], #right input[name=action_doRemoveItems], #right input[name=action_doMoveUpItems], #right input[name=action_doMoveDownItems], #right input[name=action_doAddCallbackField]').live('click', function(){
            $(this).addClass('loading')
            var silvercartMultiselectandorderFieldButtonAttributeItems = $(this);
            var form = $('#right form');
            var formAction = form.attr('action') + '?' + $(this).attr('name').replace('action_', '');

            // Post the data to save
            $.post(formAction, form.formToArray(), function(result){

                $('#right #ModelAdminPanel').html(result);

                $(silvercartMultiselectandorderFieldButtonAttributeItems).removeClass('loading');

                Behaviour.apply(); // refreshes ComplexTableField
                if(window.onresize) window.onresize();
            }, 'html');

            return false;
        });
    })(jQuery);
    /* ]]> */
</script>
