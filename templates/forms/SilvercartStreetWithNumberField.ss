<div id="{$FormName}_{$FieldName}_Box" class="type-text two-fields <% if errorMessage %> error<% end_if %><% if isRequiredField %> requiredField<% end_if %>">
    <% if errorMessage %>
        <div class="errorList">
            <% control errorMessage %>
            <strong class="message">
                {$message}
            </strong>
            <% end_control %>
        </div>
    <% end_if %>

    <label for="{$FieldID}">{$Label} $RequiredFieldMarker</label>
    {$FieldTag} <span class="spacer">/</span> $Parent.CustomHtmlFormFieldByName(StreetNumber, CustomHtmlFormFieldPlainWithoutLabel)
</div>
