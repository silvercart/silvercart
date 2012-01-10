<% control Navigation %>
    <% if hasProductsOrChildren %>
        <% if Children %>
            <h2>
                $MenuTitle.XML
            </h2>

            <div class="vlist">
                <ul>
                    <% control Children %>
                        <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                            <ul>
                                <% control Children %>
                                    <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
                                <% end_control %>
                            </ul>
                        </li>
                    <% end_control %>
                </ul>
            </div>
        <% end_if %>
    <% end_if %>
<% end_control %>
