<form class="yform" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormFieldByName(quickSearchQuery,SilvercartQuickSearchFormFields)

    $CustomHtmlFormSpecialFields

    <% control Actions %>
        $Field
    <% end_control %>
</form>