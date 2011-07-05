<% if isCustomerLoggedIn %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Regular)
    <div class="hidden-form" id="silvercart-add-address-form">
        $insertCustomHtmlForm(SilvercartAddAddressForm)
    </div>
    <a href="{$Link}addNewAddress" class="silvercart-icon-with-text-button big add16" id="silvercart-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
    <% require javascript(silvercart/script/SilvercartAddressHolder.js) %>
<% else %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Anonymous)
<% end_if %>