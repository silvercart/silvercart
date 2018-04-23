<div id="{$HolderID}" class="control-group <% if $extraClass %>{$extraClass}<% end_if %>">
    <div class="controls<% if errorMessage %> error<% end_if %>">
        <label class="checkbox inline" for="{$ID}">
            {$Field} {$Title} {$RequiredFieldMarker}
        </label>
        <% if $Message %><span class="help-inline {$MessageType}">{$Message}</span><% end_if %>
        <% if $Description %><span class="help-inline">{$Description}</span><% end_if %>
    </div>
</div>