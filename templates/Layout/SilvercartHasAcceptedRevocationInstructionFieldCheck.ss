<div id="{$FormName}_{$FieldName}_Box" class="control-group<% if errorMessage %> error<% end_if %>">
    <div class="controls<% if errorMessage %> error<% end_if %>">
        <label class="checkbox inline" for="{$FieldID}">
            {$FieldTag} <% _t('SilvercartPage.I_HAVE_READ','I have read the') %> <a href="$CurrentPage.PageByIdentifierCodeLink(SilvercartRevocationInstructionPage)" target="_blank"><% _t('SilvercartPage.REVOCATIONREAD','revocation instructions') %></a> {$RequiredFieldMarker}
        </label>
        <% if errorMessage %>
        <span class="help-inline"><i class="icon-remove"></i><% with errorMessage %> {$message}<% end_with %></span>
        <% end_if %>
    </div>
</div>
