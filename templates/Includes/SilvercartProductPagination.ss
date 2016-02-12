<% if Products && Products.MoreThanOnePage %>
<div class="pagination pagination-right">
    <% if CurrentPage.productsOnPagesString %>
    <span class="products-on-page pull-left">$CurrentPage.productsOnPagesString</span>
    <% end_if %>
    <ul>
    <% if Products.MoreThanOnePage %>
        <% if Products.NotFirstPage %>
        <li><a href="{$Products.PrevLink}#scpgpct" rel="prev" title="<% _t('SilvercartPage.PREV', 'Prev') %>"><i class="icon icon-chevron-left"></i></a></li>      
        <% end_if %>
        <% loop Products.PaginationSummary(4) %>
            <% if CurrentBool %> 
        <li class="active"><a class="highlight" href="javascript:;">$PageNum</a></li>
            <% else_if Link %>
        <li><a href="{$Link}#scpgpct" title="<% sprintf(_t('SilvercartPage.GOTO_PAGE', 'go to page %s'),$PageNum) %>">$PageNum</a></li>
            <% else %>
        <li><span>&hellip;</span></li>
            <% end_if %>
        <% end_loop %>

        <% if Products.NotLastPage %>
        <li><a href="{$Products.NextLink}#scpgpct" rel="next" title="<% _t('SilvercartPage.NEXT', 'Next') %>"><i class="icon icon-chevron-right"></i></a></li>
        <% end_if %>
    <% end_if %>
    </ul>
</div>
<% end_if %>
