<div id="{$FormName}_{$FieldName}_Box" class="type-cart<% if errorMessage %> error<% end_if %>">

    <label for="{$FormName}_{$FieldName}">{$Label}</label>
    $FieldTag

    $CustomHtmlFormSpecialFields

    <% loop Parent.Actions %>
        $Field
    <% end_loop %>
</div>
