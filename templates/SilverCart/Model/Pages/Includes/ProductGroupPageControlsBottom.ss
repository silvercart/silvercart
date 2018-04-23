<% if $HasMoreProductsThan(0) %>
    <% include SilverCart/Model/Pages/ProductPagination %>
    <div class="productFilter clearfix silvercart-product-group-page-selectors">
        <% include SilverCart/Model/Pages/ProductGroupPageControls %>
    </div>
<% end_if %>
