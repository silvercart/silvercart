<% if ShowSiblings %>
    <% if IsRootPage %>
        $ChildPages
    <% else %>
        <li class="$LinkingMode">
            <a href="$Link" title="$MenuTitle.XML" class="<% if IsActivePage %>active<% end_if %>">$MenuTitle.XML</a>
            <% if ShowChildPages %>
                <% if ChildPages %>
                    <ul class="submenu">
                        $ChildPages
                    </ul>
                <% end_if %>
            <% end_if %>
        </li>
    <% end_if %>
<% else %>
    <li class="$LinkingMode">
        <a href="$Link" title="$MenuTitle.XML" class="<% if IsActivePage %>active<% end_if %>">$MenuTitle.XML</a>
        <% if ShowChildPages %>
            <% if ChildPages %>
                <ul class="submenu">
                    $ChildPages
                </ul>
            <% end_if %>
        <% end_if %>
    </li>
<% end_if %>