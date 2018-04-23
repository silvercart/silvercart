<div id="{$HolderID}" class="control-group <% if $extraClass %>{$extraClass}<% end_if %>">
    <div class="controls">
        {$Field}
        <% if $RightTitle %><label class="help-inline" for="{$ID}">{$RightTitle}</label><% end_if %>
        <% if $Message %><span class="help-inline {$MessageType}">{$Message}</span><% end_if %>
        <% if $Description %><span class="help-inline">{$Description}</span><% end_if %>
    </div>
</div>