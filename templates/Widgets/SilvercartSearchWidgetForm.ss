<form class="yform" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormFieldByName(quickSearchQuery)
    <% loop Actions %>
        $Field
    <% end_loop %>
</form>