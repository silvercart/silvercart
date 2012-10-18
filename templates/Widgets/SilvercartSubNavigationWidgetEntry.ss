<li class="$LinkingMode">
    <a href="$Link" title="$MenuTitle.XML" class="<% if IsActivePage %>active<% end_if %>">$MenuTitle.XML</a>
    <% if ShowChildPages %>
        <% if ChildPages %>
            <ul>
                $ChildPages
            </ul>
        <% end_if %>
    <% end_if %>
</li>