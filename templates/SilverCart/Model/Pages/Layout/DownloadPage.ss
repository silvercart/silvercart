<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        <% include SilverCart/Model/Pages/DownloadPage_Table %>
     </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
