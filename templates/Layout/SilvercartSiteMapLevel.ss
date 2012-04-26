<ul>
    <li>
        <a href="$Link" title="$Title">$MenuTitle</a>
        <% if Children %>
            <% control Children %>
                $SiteMapChildren
            <% end_control %>
        <% end_if %>
    </li>
</ul>