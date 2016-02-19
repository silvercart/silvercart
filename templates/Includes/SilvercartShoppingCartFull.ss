<% if SilvercartErrors %>
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert">×</a>  
        $SilvercartErrors
    </div>
<% end_if %>
<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <% with CurrentMember %>
        <% with SilvercartShoppingCart %>
            <% if $CurrentPage.showPricesGross %>
                <% include SilvercartShoppingCartFullGross %>
            <% else %>
                <% include SilvercartShoppingCartFullNet %>
            <% end_if %>
        <% end_with %>
    <% end_with %>
<% else %>
    <div class="alert alert-error">
        <p><br /><% _t('SilvercartCartPage.CART_EMPTY', 'Your cart is empty') %></p>
    </div>
<% end_if %>
