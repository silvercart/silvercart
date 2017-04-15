<form class="quickSearch" $FormAttributes>
    <div class="input-append">
        $CustomHtmlFormMetadata
        $CustomHtmlFormFieldByName(quickSearchQuery,SilvercartQuickSearchFormFields)
        $CustomHtmlFormSpecialFields
        <% loop Actions %>
        <button class="btn btn-primary" name="quickSearchButton" type="submit">
            <i class="icon-search"></i>
        </button>   
        <% end_loop %>

    </div>   
</form>
