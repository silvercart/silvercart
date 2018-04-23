<div class="row">
<% cached $CacheKey %>
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$InsertWidgetArea(Content)}
        <% if $ViewableChildren %>
        <div class="silvercart-product-group-page-control-top">
            <% include SilverCart/Model/Pages/ProductGroupHolderControls %>
        </div>
        <div class="silvercart-product-group-page">
            {$RenderProductGroupHolderGroupView}
        </div>
        <% end_if %>
 
        <div class="silvercart-product-group-page-control-top" id="scpgpct">
            <% include SilverCart/Model/Pages/ProductGroupPageControlsTop %>
        </div>
        <div class="silvercart-product-group-page sc-products clearfix">
            {$RenderProductGroupPageGroupView}
        </div>
        <div class="silvercart-product-group-page-control-bottom">
            <% include SilverCart/Model/Pages/ProductGroupPageControlsBottom %>
        </div>
    <% if $isFirstPage %>
        {$PageContent}
    <% end_if %>
    </div>
<% end_cached %>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
   </aside>
</div>