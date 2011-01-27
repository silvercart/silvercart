<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
        $Content
        $Form
        $PageComments
        <% control Children %>
        <h3><a href="$Link">$Title</a></h3>
        <% if groupPicture %>
        <a href="$Link"><img src="$groupPicture.Link" alt="$Title" width="150"/></a>
        <% end_if %>
        <% end_control %>
        <% if randomArticles %>
        <ul>
            <% control randomArticles %>
            $articlePreviewForm
            <% end_control %>
        </ul>
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





