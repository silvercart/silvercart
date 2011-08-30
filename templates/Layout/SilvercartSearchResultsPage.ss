<div id="col1">
    <div id="col1_content" class="clearfix">
        
        <h2><% _t('SilvercartSearchResultsPage.TITLE','Suchergebnisse') %></h2>
        <% if SearchQuery %>
            <p><% sprintf(_t('SilvercartSearchResultsPage.RESULTTEXT'),$SearchQuery) %> ($TotalSearchResults <% _t('SilvercartPage.SEARCH_RESULTS', 'results') %>):</p>
        <% end_if %>

        $InsertWidgetArea(Content)
        
        <% if getProducts %>
            <% if HasMoreProductsThan(5) %>
                <div class="silvercart-product-group-page-controls">
                    <div class="silvercart-product-group-page-controls_content">
                        <% include SilvercartProductPagination %>
                        <div class="silvercart-product-group-page-selectors">
                            <div class="silvercart-product-group-page-selectors_content">
                                $InsertCustomHtmlForm(SilvercartProductGroupPageSelectors)
                            </div>
                        </div>
                    </div>
                </div>
            <% end_if %>
            $RenderProductGroupPageGroupView
            <% if HasMoreProductsThan(5) %>
                <div class="silvercart-product-group-page-controls">
                    <div class="silvercart-product-group-page-controls_content">
                        <% include SilvercartProductPagination %>
                        <div class="silvercart-product-group-page-selectors">
                            <div class="silvercart-product-group-page-selectors_content">
                                $InsertCustomHtmlForm(SilvercartProductGroupPageSelectors)
                            </div>
                        </div>
                    </div>
                </div>
            <% end_if %>
        <% else %>
            <p>
                <% _t('SilvercartPage.THE_QUERY', 'The query') %>
                <b>&rdquo;$SearchQuery&rdquo;</b>
                <% _t('SilvercartPage.DIDNOT_RETURN_RESULTS', 'did not return any results in our shop.') %>
            </p>
        <% end_if %>
        
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
