<% if SilvercartErrors %>
    <div class="silvercart-error-list">
        <div class="silvercart-error-list_content">
            $SilvercartErrors
        </div>
    </div>
<% end_if %>

<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <% control CurrentMember %>
        <% control SilvercartShoppingCart %>
            <% if Top.showPricesGross %>
                <% include SilvercartShoppingCartFullGross %>
            <% else %>
                <% include SilvercartShoppingCartFullNet %>
            <% end_if %>

            <% if Top.EditableShoppingCart %>
                <div class="shoppingCartActions">
                    <% if registeredModules %>
                        <% control registeredModules %>
                            <% if ShoppingCartActions %>
                                <% control ShoppingCartActions %>
                                    $moduleOutput
                                <% end_control %>
                            <% end_if %>
                        <% end_control %>
                    <% end_if %>
                </div>
            <% end_if %>
          
        <% end_control %>
    <% end_control %>
<% else %>
    <p><br /><% _t('SilvercartCartPage.CART_EMPTY', 'Your cart is empty') %></p>
<% end_if %>
