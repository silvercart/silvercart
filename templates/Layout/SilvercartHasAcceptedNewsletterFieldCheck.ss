<div id="{$FormName}_{$FieldName}_Box" class="type-check<% if errorMessage %> error<% end_if %>">
    <% if errorMessage %>
        <div class="errorList">
            <% control errorMessage %>
            <strong class="message">
                {$message}
            </strong>
            <% end_control %>
        </div>
    <% end_if %>
    $FieldTag
    <label for="{$FormName}_{$FieldName}">{$Label}<% if CurrentMember %><% control CurrentMember %><% if hasFinishedNewsletterOptIn %><% else %><br /><p class="silvercart-message highlighted vdistance info16"><% _t('SilvercartNewsletter.OPTIN_NOT_FINISHED_MESSAGE') %></p><% end_if %><% end_control %><% end_if %></label>
</div>
