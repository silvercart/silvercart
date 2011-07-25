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
                <div class="silvercart-checkout-notice">
                    <div class="silvercart-checkout-notice_content">
                        <% sprintf(_t('SilvercartShoppingCart.ERROR_MINIMUMORDERVALUE_NOT_REACHED'),$SilvercartShoppingCart.MinimumOrderValue) %>
                    </div>
                </div>
            <% end_if %>
        <% end_if %>
    </div>
</div>
