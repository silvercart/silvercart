<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        <% if isFirstPage %>
            $PageContent
        <% end_if %>
        
        $InsertWidgetArea(Content)
        
        <% if ViewableChildren %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupHolderControls %>
        </div>
        <div class="silvercart-product-group-page">
            $RenderProductGroupHolderGroupView
        </div>
        <% end_if %>
        
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupPageControls %>
        </div>
        <div class="silvercart-product-group-page">
            $RenderProductGroupPageGroupView
        </div>
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupPageControls %>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
