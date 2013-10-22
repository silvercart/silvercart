<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <h1>$Title</h1>
        $Content
        
        <% if ViewableChildren %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupHolderControls %>
        </div>
            $RenderProductGroupHolderGroupView
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>





