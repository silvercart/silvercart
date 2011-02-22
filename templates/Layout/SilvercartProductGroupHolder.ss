<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <h2>$Title</h2>
        $Content
        $Form
        $PageComments
        <div class="product-group-holder-toolbar clearfix">
        <% if hasMoreGroupViewsThan(1) %>
            <ul>
            <% control GroupViews %>
                <% if isActive %>
                <li class="active"><img src="$Image" alt="$Label" title="$Label" /></li>
                <% else %>
                <li><a href="{$Top.Link}switchGroupView/$Code" title="$Label"><img src="$Image" alt="$Label" title="$Label" /></a></li>
                <% end_if %>
            <% end_control %>
            </ul>
        <% end_if %>
        </div>
        $RenderProductGroupHolderGroupView
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SilvercartSideBarCart %>
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>





