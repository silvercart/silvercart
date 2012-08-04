<div id="{$FormName}_{$FieldName}_Box" class="type-text<% if errorMessage %> error<% end_if %>">
    <% if errorMessage %>
        <div class="errorList">
            <% loop errorMessage %>
            <strong class="message">
                {$message}
            </strong>
            <% end_loop %>
        </div>
    <% end_if %>

    $FieldTag
</div>