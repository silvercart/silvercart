{$Controller.ContentStep2}
<% if isCustomerLoggedIn %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Regular)
    <div class="hide" id="silvercart-add-address-form">
        <a name="silvercart-add-address-form" id="silvercart-add-address-form-scrolltarget"></a>
        $InsertCustomHtmlForm(SilvercartAddAddressForm)
    </div>
<% else %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Anonymous)
<% end_if %>