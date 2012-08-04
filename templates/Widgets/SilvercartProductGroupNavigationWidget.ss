<% cached NavigationCacheKey %>
    <% with Navigation %>
        <% with RootPage %>
            <h2>
                $MenuTitle.XML
            </h2>
        <% end_with %>

        <% if HasMenu %>
            <div class="vlist silvercart-product-group-navigation-widget">
                <ul>
                    $Menu
                </ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>