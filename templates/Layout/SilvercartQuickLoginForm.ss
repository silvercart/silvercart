<form name="QuickLogin" class="yform full" $FormAttributes>
    $CustomHtmlFormMetadata

	<div class="subcolumns">
		<div class="c50l">
            $CustomHtmlFormFieldByName(emailaddress)
		</div>

		<div class="c50r">
            $CustomHtmlFormFieldByName(password)
		</div>
    </div>

    <div id="silvercart-quicklogin-form-actions">
        <input type="reset" id="silvercart-quicklogin-form-cancel" value="<% _t('SilvercartPage.CANCEL') %>" />
        <% control Actions %>
            $Field
        <% end_control %>
    </div>
</form>
