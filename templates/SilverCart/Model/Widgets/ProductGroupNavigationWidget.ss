<% cached $NavigationCacheKey %>
    <% if $Navigation.HasMenu %>
        <% if $FrontTitle %><h3>{$FrontTitle}</h3><% end_if %>
<nav class="categories silvercart-product-group-navigation-widget">
    <ul class="unstyled">{$Navigation.Menu}</ul>
</nav>
    <% end_if %>
<% end_cached %>