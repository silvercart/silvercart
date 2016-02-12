<form class="form pull-left no-margin" {$FormAttributes} >
    {$CustomHtmlFormMetadata}
    {$CustomHtmlFormErrorMessages}
    <div class="sortBy inline pull-left">
        $CustomHtmlFormFieldByName(SortOrder,SilvercartProductGroupPageSelectorsFields)
    </div>
    <% if hasProductsPerPageOptions %>
    <div class="showItem inline pull-left">
        $CustomHtmlFormFieldByName(productsPerPage,SilvercartProductGroupPageSelectorsFields)
    </div>
    <% end_if %>
    {$CustomHtmlFormSpecialFields}
    <div class="compareItem inline pull-left">
    <% loop Actions %>
        <button class="btn btn-mini btn-primary active type-button"><i class="icon-filter"></i> {$Title}</button>
    <% end_loop %>
    </div>
</form>
