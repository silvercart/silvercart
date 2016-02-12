<div id="pre-header">
    <div class="container">
        <ul class="pull-right inline">
            <% if CurrentRegisteredCustomer %>
                <% if CurrentRegisteredCustomer.isAdmin %>
                <li><a class="highlight" href="{$baseHref}admin"><% _t('SilvercartPage.ADMIN_AREA', 'Admin Access') %></a></li>
                <li class="divider-vertical"></li>
                <% end_if %>
                <li><a class="highlight" href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><% _t('SilvercartPage.MYACCOUNT', 'my account') %></a></li>
                <li class="divider-vertical"></li>
                <li><a class="highlight" href="{$baseHref}Security/logout/"> <% _t('SilvercartPage.LOGOUT', 'Logout') %></a></li>
                <li class="divider-vertical"></li> 
            <% else %>
                <li><a class="highlight" href="$PageByIdentifierCode(SilvercartRegistrationPage).Link"><% _t('SilvercartPage.REGISTER', 'Register') %></a></li>
                <li class="divider-vertical"></li>
                <li><a class="highlight" href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><% _t('SilvercartPage.LOGIN', 'Login') %></a></li>
                <li class="divider-vertical"></li>
            <% end_if %>
        </ul>
        <% if CurrentRegisteredCustomer %>
            <p><% _t('SilvercartPage.HELLO', 'Hello') %> $CurrentRegisteredCustomer.FirstName, <a href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><% _t('SilvercartPage.MYACCOUNT', 'my account') %></a>, <a href="{$baseHref}Security/logout/"> <% _t('SilvercartPage.LOGOUT', 'Logout') %></a></p>
        <% else %>             
            <p><% _t('SilvercartPage.WELCOME_TO', 'Welcome to') %> {$SiteConfig.Title}, <a href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><% _t('SilvercartPage.LOGIN', 'Login') %></a> <% _t('SilvercartPage.OR', 'or') %> <a href="$PageByIdentifierCode(SilvercartRegistrationPage).Link"><% _t('SilvercartPage.REGISTER', 'Register') %></a></p>             
        <% end_if %>
    </div>
</div>