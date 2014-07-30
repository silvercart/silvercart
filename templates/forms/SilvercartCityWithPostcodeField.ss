<div id="{$FormName}_{$FieldName}_Box" class="type-text two-fields ltr <% if errorMessage %> error<% end_if %><% if isRequiredField %> requiredField<% end_if %>">
    <% if errorMessage %>
        <div class="errorList">
            <% with errorMessage %>
            <strong class="message">
                {$message}
            </strong>
            <% end_with %>
        </div>
    <% end_if %>

    <label for="{$FieldID}">{$Label} $RequiredFieldMarker</label>
    $Parent.CustomHtmlFormFieldByName(Postcode, CustomHtmlFormFieldPlainWithoutLabel) <span class="spacer">-</span> {$FieldTag}
</div>
