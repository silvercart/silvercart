<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <% if $CurrentRegisteredCustomer %>
            {$AddAddressForm}
        <% else %>
            <% include SilverCart/Model/Pages/MyAccountLoginOrRegister %>
        <% end_if %>
    </div>
    <aside class="span3">
    <% if $CurrentRegisteredCustomer %>
        {$SubNavigation}
    <% end_if %>
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>