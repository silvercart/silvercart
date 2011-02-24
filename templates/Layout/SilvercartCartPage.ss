<div class="shopping-cart-page">
    <% include SilvercartBreadCrumbs %>

    <h2>$Title</h2>
    <% include SilvercartShoppingCartFull %>

    <% if CurrentMember.SilvercartShoppingCart.isFilled %>
        <div class="shopping-cart-page-footer-bar">
            <a class="checkout-button" href="$PageByIdentifierCode(SilvercartCheckoutStep).Link"><% _t('SilvercartPage.CHECKOUT') %></a>
        </div>
    <% end_if %>
    $Form
    $PageComments
</div>
