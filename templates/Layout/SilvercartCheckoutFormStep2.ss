<% if isCustomerLoggedIn %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Regular)
    <div class="hidden-form" id="silvercart-add-address-form">
        $insertCustomHtmlForm(SilvercartAddAddressForm)
    </div>
    <div class="silvercart-button right">
        <div class="silvercart-button_content">
            <a href="{$Link}addNewAddress" id="silvercart-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
        </div>
    </div>
    <% require javascript(silvercart/script/SilvercartAddressHolder.js) %>
<% else %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Anonymous)
<% end_if %>