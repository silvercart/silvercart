<% if CurrentMember.SilvercartShoppingCart.isFilled %>
<div class="widget">
    <div class="widget_content side-bar-cart">
        <h3><% _t('SilvercartPage.CART') %></h3>
        <table class="full">
            <thead>
                <tr>
                    <th><% _t('SilvercartProduct.TITLE') %></th>
                    <th class="Amount"><% _t('SilvercartProductPage.QUANTITY') %></th>
                    <th class="pricewidth"><% _t('SilvercartProduct.PRICE') %></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td></td>
                    <td><% _t('SilvercartPage.SUM','sum') %></td>
                    <td class="pricewidth">$Price.Nice</td>
                </tr>
            </tfoot>
            <% control CurrentMember %>
            <% control SilvercartShoppingCart %>
            <tbody>
                <% control SilvercartShoppingCartPositions %>
                <tr>
                    <td><a href="$SilvercartProduct.Link">$SilvercartProduct.Title</a></td>
                    <td class="Amount">$Quantity</td>
                    <td class="pricewidth">$Price.Nice</td>
                </tr>
                <% end_control %>
            </tbody>
            <% end_control %>
            <% end_control %>
        </table>
        <a href="{$baseHref}warenkorb"><strong class="ShoppingCart"><% _t('SilvercartPage.GOTO_CART', 'go to cart') %></strong></a>
    </div>
</div>
<% end_if %>
