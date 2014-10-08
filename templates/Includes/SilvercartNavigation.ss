<% cached $MainNavigationCacheKey %>
    <div class="hlist">
        <ul class="Menu">
            <% with $MainNavigationRootPage %>
                <% loop Children %>
                    <% if hasProductsOrChildren %>
                    <li class="<% if LinkOrSection == 'section' %>active<% else %>{$LinkingMode}<% end_if %> <% if IsRedirectedChild %>active<% end_if %>"  >
                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO'),$Title.XML) %>" class="$LinkingMode levela"><span>$MenuTitle.XML</span></a>
                    </li>
                    <% end_if %>
                <% end_loop %>
            <% end_with %>
        </ul>
    </div>
<% end_cached %>