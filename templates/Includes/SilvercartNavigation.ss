<div class="hlist">
    <ul class="Menu">
        <% control PageByIdentifierCode(SilvercartProductGroupHolder) %>
            <% control Children %>
                <% if hasProductsOrChildren %>
                <li <% if LinkOrSection = section %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
                    <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
                </li>
                <% end_if %>
            <% end_control %>
        <% end_control %>
    </ul>
</div>
