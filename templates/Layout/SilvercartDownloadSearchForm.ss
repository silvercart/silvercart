<form class="yform full clearfix" $FormAttributes>
    {$CustomHtmlFormMetadata}
    {$CustomHtmlFormSpecialFields}
    $CustomHtmlFormFieldByName(SearchQuery)
    <% control Actions %>
        {$Field}
    <% end_control %>
</form>