<% if Products %>
    <% if Products.MoreThanOnePage %>
<div class="pagination">
    <div class="pagination_content">
        <% if Products.PrevLink %>
            <a href="$Products.PrevLink">&lt;&lt; Zur&uuml;ck</a> |
        <% end_if %>
        <% control Products.Pages %>
            <% if CurrentBool %>
                <strong>$PageNum</strong>
            <% else %>
                <a href="$Link" title="Go to page $PageNum">$PageNum</a>
            <% end_if %>
        <% end_control %>
        <% if Products.NextLink %>
        | <a href="$Products.NextLink">Vor &gt;&gt;</a>
        <% end_if %>
    </div>
</div>
    <% end_if %>
<% end_if %>
