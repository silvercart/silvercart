<div id="{$FormName}_{$FieldName}_Box" class="control-group<% if errorMessage %> error<% end_if %>">
    <div class="controls<% if errorMessage %> error<% end_if %>">
        <label class="checkbox inline" for="{$FieldID}">
            {$FieldTag} {$Label}
            <% if CurrentMember %>
                <% with CurrentMember %>
                    <% if hasFinishedNewsletterOptIn %>
                    <% else %>
                    
                    <% end_if %>
                <% end_with %>
            <% end_if %>
            {$RequiredFieldMarker}
        </label>
        <% if errorMessage %>
            <span class="help-inline"><i class="icon-remove"></i><% with errorMessage %> {$message}<% end_with %></span>
        <% end_if %>
    </div>
</div>
<div class="alert alert-info">
    <% _t('SilvercartNewsletter.OPTIN_NOT_FINISHED_MESSAGE') %>
</div>
