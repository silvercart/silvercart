<ul>
    <li>
        <a href="{$Link}" title="{$Title}">{$MenuTitle}</a>
        <% if $Children %>
            <% loop $Children %>
                {$SiteMapChildren}
            <% end_loop %>
        <% end_if %>
    </li>
</ul>