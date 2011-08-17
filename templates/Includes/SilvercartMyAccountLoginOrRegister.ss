<h2><% _t('SilvercartMyAccountHolder.ALREADY_HAVE_AN_ACCOUNT') %></h2>
$InsertCustomHtmlForm(SilvercartLoginForm)

<br />
<h2><% _t('SilvercartMyAccountHolder.WANTTOREGISTER') %></h2>
<p>
    <% _t('SilvercartMyAccountHolder.REGISTER_ADVANTAGES_TEXT') %>
</p>
<div class="silvercart-button-row">
    <div class="silvercart-button">
        <div class="silvercart-button_content">
            <a href="$RegistrationLink"><% _t('SilvercartMyAccountHolder.GOTO_REGISTRATION') %></a>
        </div>
    </div>
</div>