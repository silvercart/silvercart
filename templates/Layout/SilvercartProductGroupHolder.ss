<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
<% cached CacheKey %>
<div class="section-header clearfix">
        <h1>$Title</h1>
</div>
        $Content
        <% if ViewableChildren %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilvercartProductGroupHolderControls %>
        </div>
            $RenderProductGroupHolderGroupView
        <% end_if %>
<% end_cached %>
        </div><!--end span9-->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
   </aside><!--end aside-->
</div>