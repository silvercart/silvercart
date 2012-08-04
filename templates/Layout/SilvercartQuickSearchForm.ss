<form class="yform" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormFieldByName(quickSearchQuery,SilvercartQuickSearchFormFields)
    <% loop Actions %>
        $Field
    <% end_loop %>
</form>