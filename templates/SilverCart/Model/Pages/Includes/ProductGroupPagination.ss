<% if $ViewableChildren && $ViewableChildren.MoreThanOnePage %>
<div class="pagination pagination-right">
    <ul>
    <% if $ViewableChildren.MoreThanOnePage %>
        <% if $ViewableChildren.NotFirstPage %>
        <li><a href="{$ViewableChildren.PrevLink}" rel="prev" title="<%t SilverCart\Model\Pages\Page.PREV 'Prev' %>"><i class="icon icon-chevron-left"></i></a></li>      
        <% end_if %>
        <% loop $ViewableChildren.PaginationSummary(4) %>
            <% if $CurrentBool %> 
        <li class="active"><a class="highlight" href="javascript:;">{$PageNum}</a></li>
            <% else_if $Link %>
        <li><a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO_PAGE 'go to page {count}' count=$PageNum %>">{$PageNum}</a></li>
            <% else %>
        <li><span>&hellip;</span></li>
            <% end_if %>
        <% end_loop %>

        <% if $ViewableChildren.NotLastPage %>
        <li><a href="{$ViewableChildren.NextLink}" rel="next" title="<%t SilverCart\Model\Pages\Page.NEXT 'Next' %>"><i class="icon icon-chevron-right"></i></a></li>
        <% end_if %>
    <% end_if %>
    </ul>
</div>
<% end_if %>

