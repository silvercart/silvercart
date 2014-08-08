<% cached NavigationCacheKey %>
<% if FrontTitle %><h2>{$FrontTitle}</h2><% end_if %>
    <% with Navigation %>
        <% loop RootPage %>
            <strong class="h2">{$MenuTitle.XML}</strong>
        <% end_loop %>
        <% if HasMenu %>
            <div class="vlist silvercart-product-group-navigation-widget">
                <ul>{$Menu}</ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>