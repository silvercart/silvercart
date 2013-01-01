<form class="yform" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormFieldByName(quickSearchQuery,SilvercartQuickSearchFormFields)

    $CustomHtmlFormSpecialFields

    <% loop Actions %>
        $Field
    <% end_loop %>
</form>
