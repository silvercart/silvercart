<div id="{$HolderID}" class="control-group <% if $extraClass %>{$extraClass}<% end_if %>">
    <% if $Title %>
    <label class="control-label" for="{$ID}">{$Title}
        <% if $isRequiredField %><span class="required-field-marker">{$RequiredFieldMarker}</span><% end_if %>
    </label>
    <% end_if %>
    <div class="controls">
        {$Field}
        <% if $RightTitle %><label class="help-inline" for="{$ID}">{$RightTitle}</label><% end_if %>
        <% if $Message %><span class="help-inline {$MessageType}">{$Message}</span><% end_if %>
        <% if $Description %><span class="help-inline">{$Description}</span><% end_if %>
    </div>
</div>