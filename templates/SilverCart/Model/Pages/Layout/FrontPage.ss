<% cached 'FrontPage',$CurrentPage.MemberGroupCacheKey %>
<div class="row">
    <div class="span9">
        {$InsertWidgetArea(Content)}
    <% if $Content %>
        <hr/>
        {$Content}
    <% end_if %>
    </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
<% end_cached %>