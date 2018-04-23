<% cached $NavigationCacheKey %>
    <% with $Navigation %>
        <% if $HasMenu %>
            <% if $Up.FrontTitle %>
                <h3>{$Top.FrontTitle}</h3>
            <% end_if %>

            <div class="categories silvercart-product-group-navigation-widget">
                <ul class="unstyled">
                    {$Menu}
                </ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>