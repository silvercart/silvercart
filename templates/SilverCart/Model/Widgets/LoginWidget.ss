<% if CurrentMember.currentRegisteredCustomer %>
    <% cached WidgetCacheKey %>
    <div class="section-header clearfix">
        <h3><%t SilverCart\Model\Widgets\LoginWidget.TITLE_LOGGED_IN 'My account' %></h3>
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
        <h3><%t SilverCart\Model\Widgets\LoginWidget.TITLE_NOT_LOGGED_IN 'Login' %></h3>
    </div>

    <div class="silvercart-widget-content_frame silvercart-widget-login">
        {$LoginWidgetForm}

        <h4><%t SilverCart\Model\Pages\MyAccountHolder.WANTTOREGISTER 'Do you want to register?' %></h4>
        <p><%t SilverCart\Model\Pages\MyAccountHolder.REGISTER_ADVANTAGES_TEXT 'By registering you can reuse your data like invoice or delivery addresses on your next purchase.' %></p>
        <a class="btn btn-small pull-right" href="{$PageByIdentifierCodeLink(SilvercartRegistrationPage)}"><%t SilverCart\Model\Pages\MyAccountHolder.GOTO_REGISTRATION 'Go to the registration form' %> &raquo;</a>
    </div>
<% end_if %>