<% with Top %>
    <% if CurrentPage.Children %>
            <div class="vlist">
                <ul>
                <% loop CurrentPage.Children %>
                    <li class="$LinkingMode $FirstLast"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
                <% end_loop %>
                </ul>
            </div>
    <% end_if %>
<% end_with %>