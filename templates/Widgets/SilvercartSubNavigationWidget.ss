<% cached NavigationCacheKey %>
    <% with getNavigation %>
        <% if HasMenu %>
            <% if Top.FrontTitle %>
                <h3>$Top.FrontTitle</h3>
            <% end_if %>

            <div class="categories silvercart-product-group-navigation-widget">
                <ul class="unstyled">
                    $Menu
                </ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>