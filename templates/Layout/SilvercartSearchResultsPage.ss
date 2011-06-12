<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="typography">
            <h1><% _t('SilvercartSearchResultsPage.TITLE','Suchergebnisse') %></h1>
            <% if SearchQuery %>
                <p><% sprintf(_t('SilvercartSearchResultsPage.RESULTTEXT'),$SearchQuery) %> ($TotalSearchResults <% _t('SilvercartPage.SEARCH_RESULTS', 'results') %>):</p>
            <% end_if %>

            <% if getProducts %>
                <% include SilvercartProductPagination %>
                $RenderProductGroupPageGroupView
                <% include SilvercartProductPagination %>
            <% else %>
                <p>
                    <% _t('SilvercartPage.THE_QUERY', 'The query') %>
                    <b>&rdquo;$SearchQuery&rdquo;</b>
                    <% _t('SilvercartPage.DIDNOT_RETURN_RESULTS', 'did not return any results in our shop.') %>
                </p>
            <% end_if %>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
