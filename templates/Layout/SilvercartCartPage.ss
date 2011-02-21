<div class="ShoppingCartPage">
    <% include SilvercartBreadCrumbs %>

    <h2>$Title</h2>
    <% include SilvercartShoppingCartFull %>

    <% if CurrentMember.SilvercartShoppingCart.isFilled %>
        <div>
            <a class="detailButton" href="$PageByIdentifierCode(SilvercartCheckoutStep).Link"><strong class="ShoppingCart"><% _t('SilvercartPage.CHECKOUT') %></strong></a>
        </div>
    <% end_if %>
    $Form
    $PageComments
</div>
