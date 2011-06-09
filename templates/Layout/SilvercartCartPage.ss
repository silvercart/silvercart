<div class="shopping-cart-page">
    <% include SilvercartBreadCrumbs %>

    <h2>$Title</h2>
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
    $Form
    $PageComments
</div>
