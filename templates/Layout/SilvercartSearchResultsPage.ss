<div id="col1">
    <div id="col1_content" class="clearfix">
        
        <h1><% _t('SilvercartSearchResultsPage.TITLE','Search Results') %></h1>
        <% if EncodedSearchQuery %>
            <p><% sprintf(_t('SilvercartSearchResultsPage.RESULTTEXT'),$EncodedSearchQuery) %> ($TotalSearchResults <% _t('SilvercartPage.SEARCH_RESULTS', 'results') %>):</p>
        <% end_if %>

        $InsertWidgetArea(Content)
        
        <% if getProducts %>
            <div class="silvercart-product-group-page-control-top">
                <% include SilvercartProductGroupPageControls %>
            </div>
            $RenderProductGroupPageGroupView
            <div class="silvercart-product-group-page-control-top">
                <% include SilvercartProductGroupPageControls %>
            </div>
        <% else %>
            <p>
                <% _t('SilvercartPage.THE_QUERY', 'The query') %>
                <b>&rdquo;$EncodedSearchQuery&rdquo;</b>
                <% _t('SilvercartPage.DIDNOT_RETURN_RESULTS', 'did not return any results in our shop.') %>
            </p>
        <% end_if %>
        
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
