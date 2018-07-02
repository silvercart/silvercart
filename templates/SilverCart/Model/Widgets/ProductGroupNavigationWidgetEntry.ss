<li class="{$LinkOrSection} {$LinkingMode} level-{$Level}">
    <a href="{$Link}" title="{$Title.XML}" class="highlight <% if $IsActivePage %>active<% end_if %> level-{$Level}">{$MenuTitle.XML}</a>
    <% if $ChildPages %>
        <ul class="submenu">
            {$ChildPages}
        </ul>
    <% end_if %>
</li>