<% cached $WidgetCacheKey %>
    <% if $FrontTitle %>
<div class="section-header clearfix">
    <h3>{$FrontTitle}</h3>
</div>
<% end_if %>
    <% include SilverCart\View\GroupView\WidgetProductBoxList %>
<% end_cached %>