<% cached NavigationCacheKey %>
    <% with getNavigation %>
        <% if HasMenu %>
	    <% with Top %>
            <% if FrontTitle %>
                <h2>$FrontTitle</h2>
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
