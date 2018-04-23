{$Controller.ContentStep2}
<% if $IsCustomerLoggedIn %>
    {$CheckoutRegularCustomerAddressForm}
    <div class="hide" id="silvercart-add-address-form">
        <a name="silvercart-add-address-form" id="silvercart-add-address-form-scrolltarget"></a>
        {$AddAddressForm}
    </div>
<% else %>
    {$CheckoutAnonymousCustomerAddressForm}
<% end_if %>