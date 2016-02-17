<div class="row">
    <div class="span9">   
        <h1><% _t('SilvercartSearchResultsPage.TITLE','Search Results') %></h1>
        <% if EncodedSearchQuery %>
            <p><% sprintf(_t('SilvercartSearchResultsPage.RESULTTEXT'),$EncodedSearchQuery) %> ($TotalSearchResults <% _t('SilvercartPage.SEARCH_RESULTS', 'results') %>):</p>
        <% end_if %>

        $InsertWidgetArea(Content)
        
        <% if getProducts %>
            <div class="silvercart-product-group-page-control-top">
                <% include SilvercartProductGroupPageControlsTop %>
            </div>
            <div class="silvercart-product-group-page sc-products clearfix">
                $RenderProductGroupPageGroupView
            </div>
        <% else %>
            <p>
                <% _t('SilvercartPage.THE_QUERY', 'The query') %>
                <b>&rdquo;$EncodedSearchQuery&rdquo;</b>
                <% _t('SilvercartPage.DIDNOT_RETURN_RESULTS', 'did not return any results in our shop.') %>
            </p>
        <% end_if %>
    </div>
    <aside class="span3">
        $InsertWidgetArea(Sidebar)  
    </aside><!--end aside-->
</div>