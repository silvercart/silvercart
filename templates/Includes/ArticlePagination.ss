<% if getArticles.MoreThanOnePage %>
<div class="pagination">
    <div class="pagination_content">
        <% if getArticles.PrevLink %>
            <a href="$getArticles.PrevLink">&lt;&lt; Zur&uuml;ck</a> |
        <% end_if %>
        <% control getArticles.Pages %>
            <% if CurrentBool %>
                <strong>$PageNum</strong>
            <% else %>
                <a href="$Link" title="Go to page $PageNum">$PageNum</a>
            <% end_if %>
        <% end_control %>
        <% if getArticles.NextLink %>
        | <a href="$getArticles.NextLink">Vor &gt;&gt;</a>
        <% end_if %>
    </div>
</div>
<% end_if %>