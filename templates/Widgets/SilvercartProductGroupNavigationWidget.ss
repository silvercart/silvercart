<% cached NavigationCacheKey %>
<% if FrontTitle %><h2>{$FrontTitle}</h2><% end_if %>
    <% with Navigation %>
        <% with RootPage %>
            <strong class="h2">{$MenuTitle.XML}</strong>
        <% end_with %>
        <% if HasMenu %>
            <div class="vlist silvercart-product-group-navigation-widget">
                <ul>{$Menu}</ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>