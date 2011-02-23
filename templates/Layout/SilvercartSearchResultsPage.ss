<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="typography">
            <h1>Suchergebnisse</h1>
            <% if SearchQuery %>
                <p>Suchergebnisse f&uuml;r den Begriff <b>&rdquo;$SearchQuery&rdquo;</b> ($Products.Count <% _t('SilvercartPage.SEARCH_RESULTS', 'results') %>):
            <% end_if %>

            <% if getProducts %>
                <% include SilvercartProductPagination %>
                $RenderProductGroupPageGroupView
                <% include SilvercartProductPagination %>
            <% else %>
                <p>
                    <% _t('SilvercartPage.THE_QUERY', 'The query') %>
                    <b>$SearchQuery</b>
                    <% _t('SilvercartPage.DIDNOT_RETURN_RESULTS', 'did not return any results in our shop.') %>
                </p>
            <% end_if %>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SilvercartSideBarCart %>
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
