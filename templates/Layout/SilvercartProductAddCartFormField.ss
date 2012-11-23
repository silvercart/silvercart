<div id="{$FormName}_{$FieldName}_Box" class="type-cart<% if errorMessage %> error<% end_if %>">

    <label for="{$FormName}_{$FieldName}">{$Label}</label>
    $FieldTag
    <% control Parent.Actions %>
        $Field
    <% end_control %>
</div>