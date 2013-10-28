<% cached NavigationCacheKey %>
<% if FrontTitle %><h2>{$FrontTitle}</h2><% end_if %>
    <% control Navigation %>
        <% if HasMenu %>
            <div class="vlist silvercart-product-group-navigation-widget">
                <ul>{$Menu}</ul>
            </div>
        <% end_if %>
    <% end_control %>
<% end_cached %>