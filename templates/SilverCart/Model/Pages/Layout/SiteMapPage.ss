<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        <div class="silvercart-sitemap">
            {$SiteMap}
        </div>
        <div class="silvercartWidgetHolder">
            {$InsertWidgetArea(Content)}
        </div>
   </div>
    <aside class="span3">
        {$SubNavigation}
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
