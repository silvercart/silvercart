<form class="yform full clearfix" $FormAttributes>
    {$CustomHtmlFormMetadata}
    {$CustomHtmlFormSpecialFields}
    $CustomHtmlFormFieldByName(SearchQuery)
    <% loop Actions %>
        {$Field}
    <% end_loop %>
</form>