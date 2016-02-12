<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
            <h1>$Title</h1>
        </div>
        {$Content}
        $InsertCustomHtmlForm(SilvercartRevocationForm)
    </div>
    <aside class="span3">
        {$SubNavigation}
        $InsertWidgetArea(Sidebar)
    </aside>
</div>
