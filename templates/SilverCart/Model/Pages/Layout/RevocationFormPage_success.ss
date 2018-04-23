<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
            <h1><%t SilverCart\Model\Pages\RevocationFormPage.Success 'Successfully sent revocation' %></h1>
        </div>
        <div class="alert alert-success"><%t SilverCart\Model\Pages\RevocationFormPage.SuccessText 'Thank you! We received your revocation.' %></div>
    </div>
    <aside class="span3">
        {$SubNavigation}
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>