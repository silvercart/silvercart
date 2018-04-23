<% cached $NavigationCacheKey %>
    <% if $FrontTitle %><h3>{$FrontTitle}</h3><% end_if %>
    <% with $Navigation %>
        <% if $HasMenu %>
            <div class="categories silvercart-product-group-navigation-widget">
                <ul class="unstyled">{$Menu}</ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>