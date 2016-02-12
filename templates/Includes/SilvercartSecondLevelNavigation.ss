<% if Menu(2) %>
    <div class="widget">
         <div class="categories">
                <ul class="unstyled">
                    <% loop Menu(2) %>
                        <li class="$LinkingMode">
                            <a href="$Link" class="highlight <% if LinkingMode = current %>active <% end_if %>" title="$Title.XML">$MenuTitle.XML</a>
                            <% if Children %>
                                <ul class="submenu">
                                <% loop Children %>
                                    <li class="$LinkingMode">
                                        <a href="$Link" class="highlight <% if LinkingMode = current %>active <% end_if %>" title="$Title.XML">$MenuTitle.XML</a>
                                    </li>
                                <% end_loop %>
                                </ul>
                            <% end_if %>
                        </li>
                    <% end_loop %>
                </ul>
        </div>
    </div>
<% end_if %>