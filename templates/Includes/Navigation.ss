<div class="hlist">
    <% if Page(katalog) %>
    <ul class="Menu">
        <% control Page(mauviel) %>
        <li <% if LinkingMode = current %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
            <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela">
                <span>$MenuTitle.XML</span>
            </a>
        </li>
        <% end_control %>

        <% control Page(lsa) %>
        <li <% if LinkingMode = current %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
            <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela">
                <span>$MenuTitle.XML</span>
            </a>
        </li>
        <% end_control %>
    </ul>
    
    <% end_if %>

</div>
