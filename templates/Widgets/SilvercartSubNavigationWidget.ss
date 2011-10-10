<% control Top %>
    <% if CurrentPage.Children %>
            <div class="vlist">
                <ul>
                <% control CurrentPage.Children %>                                
                    <li class="$LinkingMode $FirstLast"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
                <% end_control %>
                </ul>
            </div>
    <% end_if %>
<% end_control %>