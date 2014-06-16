<form class="yform full" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormFieldByName(quickSearchQuery)
    <% loop Actions %>
        $Field
    <% end_loop %>
</form>