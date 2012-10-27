<% cached NavigationCacheKey %>
    <% with getNavigation %>
        <% if HasMenu %>
	    <% with Top %>
            <% if Title %>
                <h2>$Title</h2>
            <% end_if %>
	    <% end_with %>
            <div class="vlist  silvercart-product-group-navigation-widget">
                <ul>
                    $Menu
                </ul>
            </div>
        <% end_if %>
    <% end_with %>
<% end_cached %>
