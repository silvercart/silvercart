<% cached 'FrontPage',$CurrentPage.MemberGroupCacheKey %>
<div class="row">
    <div class="span9">
        {$InsertWidgetArea(Content)}
        {$Content}
    </div>
    <aside class="span3">
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
<% end_cached %>