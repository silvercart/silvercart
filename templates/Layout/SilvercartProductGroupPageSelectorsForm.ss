<div class="columnar">
<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    $CustomHtmlFormFieldByName(productsPerPage,CustomHtmlFormFieldSelect)
    $CustomHtmlFormFieldByName(SortOrder,CustomHtmlFormFieldSelect)
    <div class="type-button clearfix">
        <% control Actions %>
            $Field
        <% end_control %>
    </div>
</form>
</div>
