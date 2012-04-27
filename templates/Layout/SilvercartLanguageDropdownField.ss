<% require javascript(silvercart/script/SilvercartLanguageDropdownField.js) %>
<div id="{$FormName}_{$FieldName}_Box" class="type-language-select<% if errorMessage %> error<% end_if %><% if isRequiredField %> requiredField<% end_if %>">
    <label for="{$FieldID}">{$Label}</label>
    $FieldTag
</div>
