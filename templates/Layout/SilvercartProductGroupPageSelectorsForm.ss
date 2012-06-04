<div class="columnar">
<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    <div class="subcolumns">
        <div class="c80l">
            $CustomHtmlFormFieldByName(productsPerPage,CustomHtmlFormFieldSelect)
            $CustomHtmlFormFieldByName(SortOrder,CustomHtmlFormFieldSelect)
        </div>
    
        <div class="c20r">
            <div class="type-button clearfix">
                <% control Actions %>
                    $Field
                <% end_control %>
            </div>
        </div>
    </div>
</form>
</div>
