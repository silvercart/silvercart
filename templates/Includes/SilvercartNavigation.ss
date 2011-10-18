<div class="hlist">
    <ul class="Menu">
        <% cached 'SilvercartProductGroupNavigation', AbsoluteLink, Aggregate(PageByIdentifierCode(SilvercartProductGroupHolder).Children.Max(LastEdited))  %>
            <% control PageByIdentifierCode(SilvercartProductGroupHolder) %>
                <% control Children %>
                    <% if hasProductsOrChildren %>
                    <li <% if LinkOrSection = section %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
                        <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
                    </li>
                    <% end_if %>
                <% end_control %>
            <% end_control %>
        <% end_cached %>
    </ul>
</div>
