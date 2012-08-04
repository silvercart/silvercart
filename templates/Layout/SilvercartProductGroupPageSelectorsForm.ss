<div class="columnar">
<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    $CustomHtmlFormFieldByName(productsPerPage,CustomHtmlFormFieldSelect)
    $CustomHtmlFormFieldByName(SortOrder,CustomHtmlFormFieldSelect)
    <div class="type-button clearfix">
        <% loop Actions %>
            $Field
        <% end_loop %>
    </div>
</form>
</div>
