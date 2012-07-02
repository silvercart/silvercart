<% cached WidgetCacheKey %>
    <% if CurrentMember.currentRegisteredCustomer %>
        <h2><% _t('SilvercartLoginWidget.TITLE_LOGGED_IN') %></h2>

        <div class="silvercart-widget-content_frame">
            <% control MyAccountPage %>
                <% if Children %>
                    <ul class="vlist">
                        <% control Children %>
                            <li>
                                <a href="$Link">$MenuTitle</a>
                            </li>
                        <% end_control %>
                    </ul>
                <% end_if %>
            <% end_control %>
        </div>
    <% else %>
        <h2><% _t('SilvercartLoginWidget.TITLE_NOT_LOGGED_IN') %></h2>

        <div class="silvercart-widget-content_frame silvercart-widget-login">
            $InsertCustomHtmlForm

            <h3><% _t('SilvercartMyAccountHolder.WANTTOREGISTER') %></h3>
            <p>
                <% _t('SilvercartMyAccountHolder.REGISTER_ADVANTAGES_TEXT') %>
            </p>
            <div class="silvercart-button-row right">
                <div class="silvercart-button inline">
                    <div class="silvercart-button_content">
                        <a href="$PageByIdentifierCodeLink(SilvercartRegistrationPage)"><% _t('SilvercartMyAccountHolder.GOTO_REGISTRATION') %></a>
                    </div>
                </div>
            </div>
        </div>
    <% end_if %>
<% end_cached %>