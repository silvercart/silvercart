<div class="columnar">
<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
<% if hasProductsPerPageOptions %>
    $CustomHtmlFormFieldByName(productsPerPage,CustomHtmlFormFieldSelect)
<% end_if %>
    $CustomHtmlFormFieldByName(SortOrder,CustomHtmlFormFieldSelect)

    <div class="silvercart-products-found">
        <% sprintf(_t('SilvercartProductGroupPageSelector.PRODUCTS_FOUND'),$getTotalNumberOfProducts) %>
    </div>

    $CustomHtmlFormSpecialFields

    <div class="type-button clearfix">
        <% control Actions %>
            $Field
        <% end_control %>
    </div>
</form>
</div>
