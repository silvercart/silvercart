<% if getSilvercartProducts.MoreThanOnePage %>
<div class="pagination">
    <div class="pagination_content">
        <% if getSilvercartProductst.PrevLink %>
            <a href="$SilvercartProducts.PrevLink">&lt;&lt; Zur&uuml;ck</a> |
        <% end_if %>
        <% control getSilvercartProducts.Pages %>
            <% if CurrentBool %>
                <strong>$PageNum</strong>
            <% else %>
                <a href="$Link" title="Go to page $PageNum">$PageNum</a>
            <% end_if %>
        <% end_control %>
        <% if getSilvercartProducts.NextLink %>
        | <a href="$getSilvercartProducts.NextLink">Vor &gt;&gt;</a>
        <% end_if %>
    </div>
</div>
<% end_if %>
