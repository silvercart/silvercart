<li class="$LinkingMode">
    <a href="$Link" title="$MenuTitle.XML" class="<% if IsActivePage %>active<% end_if %>">$MenuTitle.XML</a>
    <% if ChildPages %>
        <ul>
            $ChildPages
        </ul>
    <% end_if %>
</li>