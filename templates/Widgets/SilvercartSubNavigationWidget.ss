<% cached NavigationCacheKey %>
    <% control getNavigation %>
        <% if HasMenu %>
            <% if Top.Title %>
                <h2>$Top.Title</h2>
            <% end_if %>

            <div class="vlist  silvercart-product-group-navigation-widget">
                <ul>
                    $Menu
                </ul>
            </div>
        <% end_if %>
    <% end_control %>
<% end_cached %>