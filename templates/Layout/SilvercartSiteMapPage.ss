<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <h1>{$Title}</h1>

        $Content

        <div class="silvercart-sitemap">
            $SiteMap
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
