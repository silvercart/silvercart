<% cached NavigationCacheKey %>
    <% control Navigation %>
        <% control RootPage %>
            <h2>
                $MenuTitle.XML
            </h2>
        <% end_control %>

        <% if HasMenu %>
            <div class="vlist silvercart-product-group-navigation-widget">
                <ul>
                    $Menu
                </ul>
            </div>
        <% end_if %>
    <% end_control %>
<% end_cached %>