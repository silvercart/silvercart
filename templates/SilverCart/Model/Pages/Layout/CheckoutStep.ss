<div class="row">
    <div class="span9">
        {$Content}
        <% if $Form %>
        <div class="form">
            {$Form}
        </div>
        <% end_if %>
        <div class="silvercartWidgetHolder">
            {$InsertWidgetArea(Content)}
        </div>
    </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
