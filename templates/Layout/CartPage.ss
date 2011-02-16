<div class="ShoppingCartPage">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
        $Content
        <% include ShoppingCartFull %>
        <% if isFilledCart %>
        <div>
            <a class="detailButton" href="$PageByClassName(CheckoutStep).Link"><strong class="ShoppingCart"><% _t('Page.CHECKOUT') %></strong></a>
        </div>
        <% end_if %>
        $Form
        $PageComments
</div>
