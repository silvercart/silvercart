<div id="{$HolderID}" class="form-group <% if $extraClass %>{$extraClass}<% end_if %>">
    {$addErrorClass('is-invalid').Field}
    <% if $Message %><div class="<% if $ValidationFailed %>invalid-feedback<% else %>valid-feedback<% end_if %> {$MessageType}">{$Message}</div><% end_if %>
    <% if $RightTitle %><small class="form-text text-muted">{$RightTitle}</small><% end_if %>
    <% if $Description %><small class="form-text text-muted">{$Description}</small><% end_if %>
</div>