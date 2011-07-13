<h2><% _t('SilvercartShoppingcartWidget.TITLE') %></h2>

<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <table class="full">
        <thead>
            <tr>
                <th><% _t('SilvercartProduct.TITLE') %></th>
                <th class="side-bar-cart-amount"><% _t('SilvercartProduct.QUANTITY_SHORT') %></th>
                <th class="side-bar-cart-price"><% _t('SilvercartProduct.PRICE') %></th>
            </tr>
        </thead>
        <% control CurrentMember %>
        <% control SilvercartShoppingCart %>
        <tfoot>
            <tr>
                <td class="side-bar-cart-price" colspan="2"><strong><% _t('SilvercartPage.SUM','sum') %></strong></td>
                <td class="side-bar-cart-price">$AmountTotal.Nice</td>
            </tr>
        </tfoot>
        <tbody>
            <% control SilvercartShoppingCartPositions %>
            <tr>
                <td><a href="$SilvercartProduct.Link">$SilvercartProduct.Title</a></td>
                <td class="side-bar-cart-amount">$Quantity</td>
                <td class="side-bar-cart-price">$Price.Nice</td>
            </tr>
            <% end_control %>
        </tbody>
        <% end_control %>
        <% end_control %>
    </table>
    <div class="subcolumns">
        <a href="$CartLink" style="float:left;"><strong class="ShoppingCart"><% _t('SilvercartPage.GOTO_CART', 'go to cart') %></strong></a>
        <a href="$CheckOutLink" style="float:right;"><strong class="ShoppingCart"><% _t('SilvercartPage.CHECKOUT', 'checkout') %></strong></a>
    </div>
<% else %>
    <p>
        <% _t('SilvercartCartPage.CART_EMPTY') %>
    </p>
<% end_if %>