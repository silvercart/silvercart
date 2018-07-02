<% if $Message %>
    <div id="{$FormName}_message" class="alert alert-{$MessageType} message {$MessageType}">{$Message}</div>
<% else %>
    <div id="{$FormName}_message" class="alert alert-error message" style="display: none"></div>
<% end_if %>