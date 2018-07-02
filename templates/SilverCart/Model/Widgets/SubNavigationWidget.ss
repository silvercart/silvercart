<% cached $NavigationCacheKey %>
    <% if $Navigation.HasMenu %>
        <% if $FrontTitle %><h3>{$FrontTitle}</h3><% end_if %>
<div class="categories silvercart-product-group-navigation-widget">
    <ul class="unstyled">{$Navigation.Menu}</ul>
</div>
    <% end_if %>
<% end_cached %>