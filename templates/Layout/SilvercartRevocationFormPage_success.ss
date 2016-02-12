<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
            <h1><% _t('SilvercartRevocationFormPage.Success') %></h1>
        </div>
        <div class="alert alert-success"><% _t('SilvercartRevocationFormPage.SuccessText') %></div>
    </div>
    <aside class="span3">
        {$SubNavigation}
        $InsertWidgetArea(Sidebar)
    </aside>
</div>