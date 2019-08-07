<% cached $ProductGroup.ID, $ProductGroup.MemberGroupCacheKey %>
    <% with $ProductGroup %>
        <% if $Children %>
            <% include SilverCart/Model/Pages/NavigationSubmenu %>
        <% end_if %>
    <% end_with %>
<% end_cached %>