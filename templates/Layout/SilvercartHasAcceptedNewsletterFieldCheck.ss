<div id="{$FormName}_{$FieldName}_Box" class="type-check<% if errorMessage %> error<% end_if %>">
    <% if errorMessage %>
        <div class="errorList">
            <% with errorMessage %>
            <strong class="message">
                {$message}
            </strong>
            <% end_with %>
        </div>
    <% end_if %>
    $FieldTag
    <label for="{$FormName}_{$FieldName}">{$Label}<% if CurrentMember %><% with CurrentMember %><% if hasFinishedNewsletterOptIn %><% else %><br /><span class="silvercart-message highlighted vdistance info16"><% _t('SilvercartNewsletter.OPTIN_NOT_FINISHED_MESSAGE') %></span><% end_if %><% end_with %><% end_if %></label>
</div>
