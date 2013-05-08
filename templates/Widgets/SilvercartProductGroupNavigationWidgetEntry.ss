<li class="{$LinkOrSection} {$LinkingMode} level-{$Level}">
    <a href="$Link" title="$MenuTitle.XML" class="<% if IsActivePage %>active<% end_if %> level-{$Level}">$MenuTitle.XML</a>
    <% if ChildPages %>
        <ul>
            $ChildPages
        </ul>
    <% end_if %>
</li>