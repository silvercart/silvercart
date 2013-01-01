<div class="columnar">
<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    $CustomHtmlFormFieldByName(productsPerPage,CustomHtmlFormFieldSelect)
    $CustomHtmlFormFieldByName(SortOrder,CustomHtmlFormFieldSelect)

    <div class="silvercart-products-found">
        <% sprintf(_t('SilvercartProductGroupPageSelector.PRODUCTS_FOUND'),$getTotalNumberOfProducts) %>
    </div>

    $CustomHtmlFormSpecialFields

    <div class="type-button clearfix">
        <% loop Actions %>
            $Field
        <% end_loop %>
    </div>
</form>
</div>
