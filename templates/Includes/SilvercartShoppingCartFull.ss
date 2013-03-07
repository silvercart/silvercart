<% if SilvercartErrors %>
    <div class="silvercart-error-list">
        <div class="silvercart-error-list_content">
            $SilvercartErrors
        </div>
    </div>
<% end_if %>

<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <% with CurrentMember %>
        <% with SilvercartShoppingCart %>
            <% if CurrentPage.showPricesGross %>
                <% include SilvercartShoppingCartFullGross %>
            <% else %>
                <% include SilvercartShoppingCartFullNet %>
            <% end_if %>

            <% if CurrentPage.EditableShoppingCart %>
                <div class="shoppingCartActions">
                    <% if registeredModules %>
                        <% loop registeredModules %>
                            <% if ShoppingCartActions %>
                                <% loop ShoppingCartActions %>
                                    $moduleOutput
                                <% end_loop %>
                            <% end_if %>
                        <% end_loop %>
                    <% end_if %>
                </div>
            <% end_if %>
          
        <% end_with %>
    <% end_with %>
<% else %>
    <p><br /><% _t('SilvercartCartPage.CART_EMPTY', 'Your cart is empty') %></p>
<% end_if %>
