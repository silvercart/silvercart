<div id="{$FormName}_{$FieldName}_Box" class="control-group<% if errorMessage %> error<% end_if %>">
    <div class="controls<% if errorMessage %> error<% end_if %>">
        <label class="checkbox inline" for="{$FieldID}">
            {$FieldTag} <% _t('SilvercartPage.I_ACCEPT','I accept the') %> <a href="$CurrentPage.PageByIdentifierCodeLink(TermsOfServicePage)" target="_blank"><% _t('SilvercartPage.TITLE_TERMS') %></a> {$RequiredFieldMarker}
        </label>
        <% if errorMessage %>
        <span class="help-inline"><i class="icon-remove"></i><% with errorMessage %> {$message}<% end_with %></span>
        <% end_if %>
    </div>
</div>
