<% if isCustomerLoggedIn %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Regular)
<% else %>
    $InsertCustomHtmlForm(SilvercartCheckoutFormStep2Anonymous)
<% end_if %>