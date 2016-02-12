<% if HasMoreProductsThan(0) %>
    <% include SilvercartProductPagination %>
        <div class="productFilter clearfix silvercart-product-group-page-selectors">
            <% include SilvercartProductGroupPageControls %>
            <%--   $InsertCustomHtmlForm(SilvercartProductGroupPageSelectorsBottom) --%>
        </div>
<% end_if %>
