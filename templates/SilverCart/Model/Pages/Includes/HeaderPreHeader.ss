<div id="pre-header">
    <div class="container">
        <ul class="pull-right inline">
            <% if CurrentRegisteredCustomer %>
                <% if CurrentRegisteredCustomer.isAdmin %>
                <li><a class="highlight" href="{$baseHref}admin"><%t SilverCart\Model\Pages\Page.ADMIN_AREA 'Admin Access' %></a></li>
                <li class="divider-vertical"></li>
                <% end_if %>
                <li><a class="highlight" href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><%t SilverCart\Model\Pages\Page.MYACCOUNT 'my account' %></a></li>
                <li class="divider-vertical"></li>
                <li><a class="highlight" href="{$baseHref}Security/logout/"> <%t SilverCart\Model\Pages\Page.LOGOUT 'Logout' %></a></li>
                <li class="divider-vertical"></li> 
            <% else %>
                <li><a class="highlight" href="$PageByIdentifierCode(SilvercartRegistrationPage).Link"><%t SilverCart\Model\Pages\Page.REGISTER 'Register' %></a></li>
                <li class="divider-vertical"></li>
                <li><a class="highlight" href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><%t SilverCart\Model\Pages\Page.LOGIN 'Login' %></a></li>
                <li class="divider-vertical"></li>
            <% end_if %>
        </ul>
        <% if CurrentRegisteredCustomer %>
            <p><%t SilverCart\Model\Pages\Page.HELLO 'Hello' %> $CurrentRegisteredCustomer.FirstName, <a href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><%t SilverCart\Model\Pages\Page.MYACCOUNT 'my account' %></a>, <a href="{$baseHref}Security/logout/"> <%t SilverCart\Model\Pages\Page.LOGOUT 'Logout' %></a></p>
        <% else %>             
            <p><%t SilverCart\Model\Pages\Page.WELCOME_TO 'Welcome to' %> {$SiteConfig.Title}, <a href="$PageByIdentifierCode(SilvercartMyAccountHolder).Link"><%t SilverCart\Model\Pages\Page.LOGIN 'Login' %></a> <%t SilverCart\Model\Pages\Page.OR 'or' %> <a href="$PageByIdentifierCode(SilvercartRegistrationPage).Link"><%t SilverCart\Model\Pages\Page.REGISTER 'Register' %></a></p>             
        <% end_if %>
    </div>
</div>