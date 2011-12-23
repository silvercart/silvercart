<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        $PageContent
        $InsertWidgetArea(Content)
        
        <% if ViewableChildren.MoreThanOnePage %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupHolderControls %>
        </div>
        <% end_if %>

        <div class="silvercart-product-group-page">
            $RenderProductGroupHolderGroupView
        </div>
        
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupPageControls %>
        </div>
        
        <div class="silvercart-product-group-page">
            $RenderProductGroupPageGroupView
        </div>
	
        <% if ViewableChildren.MoreThanOnePage %>
        <div class="silvercart-product-group-page-control-bottom">
            <% include SilvercartProductGroupHolderControls %>
        </div>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
