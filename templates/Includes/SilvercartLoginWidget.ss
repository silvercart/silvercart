<% if CurrentMember %>
    <h2><% _t('SilvercartLoginWidget.TITLE_LOGGED_IN') %></h2>

    <% control MyAccountPage %>
        <% if Children %>
            <% control Children %>
                <p>
                    <a href="$Link">$MenuTitle</a>
                </p>
            <% end_control %>
        <% end_if %>
    <% end_control %>
<% else %>
    <h2><% _t('SilvercartLoginWidget.TITLE_NOT_LOGGED_IN') %></h2>

    $InsertCustomHtmlForm

    <h3><% _t('SilvercartMyAccountHolder.WANTTOREGISTER') %></h3>
    <p>
        <% _t('SilvercartMyAccountHolder.REGISTER_ADVANTAGES_TEXT') %>
    </p>
    <div class="button-big">
        <a href="$RegistrationLink"><% _t('SilvercartMyAccountHolder.GOTO_REGISTRATION') %></a>
    </div>
<% end_if %>