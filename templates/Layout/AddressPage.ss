<div id="col1">
    <div id="col1_content" class="clearfix">
        <div id="Breadcrumbs">
            <p>$getBreadcrumbs</p>
        </div>

        <h2>$Title</h2>

        $Content
        $SearchResults
        $Form
        $InsertCustomHtmlForm(EditAddressForm)
        $PageComments
    </div>
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>
<% end_if %>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SecondLevelNavigation %>
        <% include SideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>






