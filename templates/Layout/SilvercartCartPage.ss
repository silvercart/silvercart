<div id="col4">
    <div id="col4_content" class="clearfix">
        
        <% include SilvercartBreadCrumbs %>
        <% include SilvercartShoppingCartFull %>

        <% if CurrentMember.SilvercartShoppingCart.isFilled %>
            <% if CurrentMember.SilvercartShoppingCart.IsMinimumOrderValueReached %>
                <div class="shopping-cart-page-footer-bar">
                    <a class="checkout-button" href="$PageByIdentifierCode(SilvercartCheckoutStep).Link"><% _t('SilvercartPage.CHECKOUT') %></a>
                </div>
            <% else %>
                <p>Der Mindestbestellwert beträgt ...</p>
            <% end_if %>
        <% end_if %>
    </div>
</div>
