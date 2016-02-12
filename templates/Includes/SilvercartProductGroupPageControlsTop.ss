<% if HasMoreProductsThan(0) %>
    <% include SilvercartProductPagination %>
    <div class="productFilter clearfix silvercart-product-group-page-selectors">
        $InsertCustomHtmlForm(SilvercartProductGroupPageSelectors)   
        <% include SilvercartProductGroupPageControls %>    
    </div>
<% end_if %>


