<form name="QuickLogin" class="yform" $FormAttributes>
    $CustomHtmlFormMetadata

	<div class="subcolumns">
		<div class="c50l">
			<div class="subcl">
				<div class="Head_line">
					<% _t('SilvercartPage.EMAIL_ADDRESS') %>
				</div>
					$CustomHtmlFormFieldByName(emailaddress,SilvercartQuickLoginFormFields)
			</div>
		</div>

		<div class="c40l">
			<div class="subcl">
				<div class="Head_line">
					<% _t('SilvercartPage.PASSWORD','password') %>:
				</div>
					$CustomHtmlFormFieldByName(password,SilvercartQuickLoginFormFields)
			</div>
		</div>

		<div class="c10r">
			<div class="subcr">
					<% control Actions %>
						<div id="SendAction">
							$Field
						</div>
					<% end_control %>
			</div>
		</div>
		$CustomHtmlFormErrorMessages
	</div>
</form>
