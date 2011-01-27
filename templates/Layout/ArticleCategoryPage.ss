<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
		$Content
		$Form
		$PageComments
        <% if CategoriesArticles %>
        <ul id="articles">
            <% control CategoriesArticles %>
            $articlePreviewForm
            <% end_control %>
        </ul>
        <% end_if %>
        <% if CategoriesArticles.MoreThanOnePage %>
        <% if CategoriesArticles.PrevLink %><a style="float: left" href="$CategoriesArticles.PrevLink">Zur&uuml;ck</a><% end_if %>
        <% if CategoriesArticles.NextLink %><a style="float: right" href="$CategoriesArticles.NextLink">Vor</a><% end_if %>
        <% end_if %>
    </div>
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>
<% end_if %>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SideBarCart %>
        <% include ThirdLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
