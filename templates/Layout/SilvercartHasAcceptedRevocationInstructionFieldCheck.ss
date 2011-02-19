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
    <label for="{$FormName}_{$FieldName}"><% _t('SilvercartPage.I_HAVE_READ','I have read the') %> <a href="$PageByIdentifierCode(SilvercartDataPrivacyStatementPage).Link"><% _t('SilvercartPage.REVOCATION','revocation instructions') %>:</a></label>
    $FieldTag
</div>
