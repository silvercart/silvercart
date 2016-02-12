<% if CurrentMember.currentRegisteredCustomer %>
    <% cached WidgetCacheKey %>
    <div class="section-header clearfix">
        <h3><% _t('SilvercartLoginWidget.TITLE_LOGGED_IN') %></h3>
    </div>

    <div class="categories silvercart-widget-content_frame">
        <% with MyAccountPage %>
            <% if Children %>
                <ul class="unstyled">
                    <% loop Children %>
                        <li>
                            <a href="$Link">$MenuTitle</a>
                        </li>
                    <% end_loop %>
                </ul>
            <% end_if %>
        <% end_with %>
    </div>
    <% end_cached %>
<% else %>
    <div class="section-header clearfix">
        <h3><% _t('SilvercartLoginWidget.TITLE_NOT_LOGGED_IN') %></h3>
    </div>

    <div class="silvercart-widget-content_frame silvercart-widget-login">
        $InsertCustomHtmlForm

        <h4><% _t('SilvercartMyAccountHolder.WANTTOREGISTER') %></h4>
        <p><% _t('SilvercartMyAccountHolder.REGISTER_ADVANTAGES_TEXT') %></p>
        <a class="btn btn-small pull-right" href="$PageByIdentifierCodeLink(SilvercartRegistrationPage)"><% _t('SilvercartMyAccountHolder.GOTO_REGISTRATION') %> &raquo;</a>
    </div>
<% end_if %>