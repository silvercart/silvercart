<div id="col4">
    <div id="col4_content" class="clearfix">
        
        <% include SilvercartBreadCrumbs %>
        <% include SilvercartShoppingCartFull %>

        <% if CurrentMember.SilvercartShoppingCart.isFilled %>
            <% if CurrentMember.SilvercartShoppingCart.IsMinimumOrderValueReached %>
                <div class="silvercart-button">
                    <div class="silvercart-button_content">
                        <a href="$PageByIdentifierCode(SilvercartCheckoutStep).Link"><% _t('SilvercartPage.CHECKOUT') %></a>
                    </div>
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
