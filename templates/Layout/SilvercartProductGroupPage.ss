<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
              <h1>$Title</h1>  
        </div>
        
        $InsertWidgetArea(Content)
        
        <% if ViewableChildren %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupHolderControls %>
        </div>
        <div class="silvercart-product-group-page">
            $RenderProductGroupHolderGroupView
        </div>
        <% end_if %>
 
        <% cached CacheKey %>
        <div class="silvercart-product-group-page-control-top" id="scpgpct">
            <% include SilvercartProductGroupPageControlsTop %>
        </div>
        <div class="silvercart-product-group-page sc-products clearfix">
            $RenderProductGroupPageGroupView
        </div>
        <div class="silvercart-product-group-page-control-bottom">
            <% include SilvercartProductGroupPageControlsBottom %>
        </div>
        <% end_cached %>
    
        <% if isFirstPage %>
            $PageContent
        <% end_if %>

    </div>
    <!-- end span9 -->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
   </aside>
        <!-- end aside -->
</div>