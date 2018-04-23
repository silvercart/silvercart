<div class="row">
    <div class="span9">
        <div id="silvercart-breadcrumbs-id" class="silvercart-breadcrumbs clearfix">
            <p>{$Breadcrumbs}</p>
        </div>

        <% if $CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1><%t SilverCart\Model\Customer\Address.EDITADDRESS 'Edit address' %></h1>
        </div>
            {$EditAddressForm}
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