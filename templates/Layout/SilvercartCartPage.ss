<div class="ShoppingCartPage">
        <% include SilvercartBreadCrumbs %>
        <h2>$Title</h2>
        $Content
        <% include SilvercartShoppingCartFull %>
        <% if isFilledCart %>
        <div>
            <a class="detailButton" href="$PageByClassName(SilvercartCheckoutStep).Link"><strong class="ShoppingCart"><% _t('SilvercartPage.CHECKOUT') %></strong></a>
        </div>
        <% end_if %>
        $Form
        $PageComments
</div>
