<div class="ShoppingCartPage">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
        $Content
        <% if isFilledCart %>
        <% include ShoppingCartFull %>
        <div>
            <a class="detailButton" href="$PageByClassName(CheckoutStep).Link"><strong class="ShoppingCart"><% _t('Page.CHECKOUT') %></strong></a>
        </div>
        <% else %>
    <p><% _t('CartPage.CART_EMPTY', 'Your cart is empty') %></p>
<% end_if %>
        $Form
        $PageComments
</div>
