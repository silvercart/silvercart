<% cached NavigationCacheKey %>
    <% control getNavigation %>
        <% if HasMenu %>
            <% if Top.FrontTitle %>
                <strong class="h2">$Top.FrontTitle</strong>
            <% end_if %>

            <div class="vlist  silvercart-product-group-navigation-widget">
                <ul>
                    $Menu
                </ul>
            </div>
        <% end_if %>
    <% end_control %>
<% end_cached %>