<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="subcolumns">
            <div class="c50l">
                <% include SilvercartBreadCrumbs %>
            </div>
            <div class="c50r">
                <% include SilvercartProductPagination %>
            </div>
        </div>
        
        $Content

        <% if Children %>
        <div class="product-group-holder-toolbar clearfix">
        <% if hasMoreGroupHolderViewsThan(1) %>
            <ul>
            <% control GroupHolderViews %>
                <% if isActiveHolder %>
                <li class="active"><img src="$Image" alt="$Label" title="$Label" /></li>
                <% else %>
                <li><a href="{$Top.Link}switchGroupHolderView/$Code" title="$Label"><img src="$Image" alt="$Label" title="$Label" /></a></li>
                <% end_if %>
            <% end_control %>
            </ul>
        <% end_if %>
        </div>
        <% end_if %>
        $RenderProductGroupHolderGroupView
        <% if ActiveSilvercartProducts %>
        <div class="product-group-holder-toolbar clearfix">
        <% if hasMoreGroupViewsThan(1) %>
            <ul>
            <% control GroupViews %>
                <% if isActive %>
                <li class="active"><img src="{$BaseHref}$Image" alt="$Label" title="$Label" /></li>
                <% else %>
                <li><a href="{$Top.Link}switchGroupView/$Code" title="$Label"><img src="{$BaseHref}$Image" alt="$Label" title="$Label" /></a></li>
                <% end_if %>
            <% end_control %>
            </ul>
        <% end_if %>
        </div>
        <% end_if %>
        $RenderProductGroupPageGroupView
        <% include SilvercartProductPagination %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
