<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <h2>$Title</h2>
		$Content
		$Form
		$PageComments
        <% if CategoriesProducts %>
        <ul id="products">
            <% control CategoriesProducts %>
            $productPreviewForm
            <% end_control %>
        </ul>
        <% end_if %>
        <% if CategoriesProducts.MoreThanOnePage %>
        <% if CategoriesProducts.PrevLink %><a style="float: left" href="$CategoriesProducts.PrevLink"><% _t('Page.PREV','prev') %></a><% end_if %>
        <% if CategoriesProducts.NextLink %><a style="float: right" href="$CategoriesProducts.NextLink"><% _t('Page.NEXT','next') %></a><% end_if %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SilvercartSideBarCart %>
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
