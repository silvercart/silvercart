<div class="hlist">
    <ul class="Menu">
<% control PageByClassName(SilvercartProductGroupHolder) %>
    <% control Children %>
        <% if hasProductsOrChildren %>
        <li <% if LinkingMode = current %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
            <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
        </li>
        <% end_if %>
    <% end_control %>
<% end_control %>
    </ul>
</div>