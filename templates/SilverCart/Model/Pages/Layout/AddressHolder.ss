<div class="row-fluid">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>

    <% if $ErrorMessage %>
        <p class="alert alert-error">{$ErrorMessage}</p>
    <% end_if %>
    <% if $SuccessMessage %>
        <p class="alert alert-success">{$SuccessMessage}</p>
    <% end_if %>

    <% if $CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1><%t SilverCart\Model\Pages\AddressHolder.CURRENT_DEFAULT_ADDRESSES 'Your default invoice and shipping addresses' %></h1>
        </div>
        {$Content}
        <% with $CurrentRegisteredCustomer %>
            <% if $hasOnlyOneStandardAddress %>
                <% with $InvoiceAddress %>
                    <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
                <% end_with %>
            <% else %>
        <div class="row-fluid silvercart-address-equalize">
            <div class="span4">
                <% with $InvoiceAddress %>
                    <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
                <% end_with %>
            </div>
            <div class="span4">
                <% with $ShippingAddress %>
                    <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
                <% end_with %>
            </div>
        </div>
            <% end_if %>
        <div class="silvercart-address-equalize">
            <% include SilverCart/Model/Pages/AddressDetail %>
        </div>
        <% end_with %>
        <hr />
        <div class="hidden-form" id="silvercart-add-address-form">
            {$AddAddressForm}
        </div>
        <a class="btn btn-small" href="{$Link(addNewAddress)}" id="silvercart-add-address-link"><%t SilverCart\Model\Pages\AddressHolder.ADD 'Add new address' %></a>
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