<div class="ShoppingCartPage">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
        $Content
        <% include ShoppingCart %>
        <% if isFilledCart %>
        <div>
            <a class="detailButton" href="{$BaseHref}checkout/"><strong class="ShoppingCart">Zur Kasse</strong></a>
        </div>
        <% end_if %>
        $Form
        $PageComments
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>
<% end_if %>
