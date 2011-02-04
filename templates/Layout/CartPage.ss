<div class="ShoppingCartPage">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
        $Content
        <% include ShoppingCart %>
        <% if isFilledCart %>
        <div>
            <a class="detailButton" href="{$BaseHref}checkout/"><strong class="ShoppingCart"><% _t('Page.CHECKOUT') %></strong></a>
        </div>
        <% end_if %>
        $Form
        $PageComments
</div>
