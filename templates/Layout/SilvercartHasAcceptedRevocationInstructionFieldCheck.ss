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
    <label for="{$FormName}_{$FieldName}"><% _t('SilvercartPage.I_HAVE_READ','I have read the') %> <a href="$CurrentPage.PageByIdentifierCodeLink(TermsOfServicePage)"><% _t('SilvercartPage.REVOCATIONREAD','revocation instructions') %></a></label>
</div>
