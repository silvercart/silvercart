<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupPageControls %>
        </div>
        
        $Content
        $RenderProductGroupPageGroupView
        
        <div class="silvercart-product-group-page-control-bottom">
            <% include SilvercartProductGroupPageControls %>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
