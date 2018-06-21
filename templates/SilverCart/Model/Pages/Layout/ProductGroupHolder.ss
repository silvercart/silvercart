<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
<% cached $CacheKey %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
    <% if $ViewableChildren %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilverCart/Model/Pages/ProductGroupHolderControls %>
        </div>
        {$RenderProductGroupHolderGroupView}
    <% end_if %>
<% end_cached %>
    </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
   </aside>
</div>