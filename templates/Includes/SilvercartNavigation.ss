<% cached 'SilvercartNavigation',List(SilvercartProductGroupPage).max(LastEdited),ID,i18nLocale %>
    <div class="hlist">
        <ul class="Menu">
            <% with PageByIdentifierCode(SilvercartProductGroupHolder) %>
                <% loop Children %>
                    <% if hasProductsOrChildren %>
                    <li <% if LinkOrSection = section %> class="active" <% else %> class="$LinkingMode"<% end_if %> >
                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
                    </li>
                    <% end_if %>
                <% end_loop %>
            <% end_with %>
        </ul>
    </div>
<% end_cached %>