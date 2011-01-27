<% if Menu(2) %>
    <div class="widget">
        <div class="widget_content">
            <div class="vlist">
                <ul>
                    <% control Menu(2) %>
                        <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                            <% if Children %>
                                <ul>
                                <% control Children %>
                                    <li class="$LinkingMode">
                                        <a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                                    </li>
                                <% end_control %>
                                </ul>
                            <% end_if %>
                        </li>
                    <% end_control %>
                </ul>
            </div>
        </div>
    </div>
<% end_if %>