<% if Menu(2) %>
    <div class="widget">
        <div class="widget_content">
            <div class="vlist">
                <ul>
                    <% loop Menu(2) %>
                        <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                            <% if Children %>
                                <ul>
                                <% loop Children %>
                                    <li class="$LinkingMode">
                                        <a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                                    </li>
                                <% end_loop %>
                                </ul>
                            <% end_if %>
                        </li>
                    <% end_loop %>
                </ul>
            </div>
        </div>
    </div>
<% end_if %>