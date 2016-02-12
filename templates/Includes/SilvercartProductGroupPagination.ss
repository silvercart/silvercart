<% if ViewableChildren && ViewableChildren.MoreThanOnePage %>
<div class="pagination pagination-right">
    <ul>
    <% if ViewableChildren.MoreThanOnePage %>
        <% if ViewableChildren.NotFirstPage %>
        <li><a href="{$ViewableChildren.PrevLink}" rel="prev" title="<% _t('SilvercartPage.PREV', 'Prev') %>"><i class="icon icon-chevron-left"></i></a></li>      
        <% end_if %>
        <% loop ViewableChildren.PaginationSummary(4) %>
            <% if CurrentBool %> 
        <li class="active"><a class="highlight" href="javascript:;">$PageNum</a></li>
            <% else_if Link %>
        <li><a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO_PAGE', 'go to page %s'),$PageNum) %>">$PageNum</a></li>
            <% else %>
        <li><span>&hellip;</span></li>
            <% end_if %>
        <% end_loop %>

        <% if ViewableChildren.NotLastPage %>
        <li><a href="{$ViewableChildren.NextLink}" rel="next" title="<% _t('SilvercartPage.NEXT', 'Next') %>"><i class="icon icon-chevron-right"></i></a></li>
        <% end_if %>
    <% end_if %>
    </ul>
</div>
<% end_if %>

