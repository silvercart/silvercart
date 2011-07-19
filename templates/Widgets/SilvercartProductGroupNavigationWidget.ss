<% control Navigation %>
    <% if hasProductsOrChildren %>
        <% if Children %>
            <h2>
                <a class="active" href="$Link"  title="$Title.XML">$MenuTitle.XML</a>
            </h2>

            <div class="vlist">
                <ul>
                    <li class="$LinkingMode">
                        <ul>
                            <% control Children %>
                                <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
                            <% end_control %>
                        </ul>
                    </li>
                </ul>
            </div>
        <% end_if %>
    <% end_if %>
<% end_control %>
