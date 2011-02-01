<div class="hlist">
<% if Page(articlegroups) %>
    <ul class="Menu">
    <% control ChildrenOf(articlegroups) %>
        <% if hasArticlesOrChildren %>
        <li <% if LinkingMode = current %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
            <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela">
                <span>$MenuTitle.XML</span>
            </a>
        </li>
        <% end_if %>
    <% end_control %>
    </ul>
<% end_if %>
</div>