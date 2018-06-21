<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        {$DownloadSearchForm}
<% if $Children %>
    <% loop $Children %>
        <h2>{$Title}</h2>
        <% if $Content %>
            {$Content}
        <% end_if %>
        <% include SilverCart/Model/Pages/DownloadPage_Table %>
    <% end_loop %>
<% end_if %>
    </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>