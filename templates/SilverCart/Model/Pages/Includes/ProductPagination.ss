<% if $getProducts && $getProducts.MoreThanOnePage %>
<div class="pagination pagination-right">
    <% if $CurrentPage.productsOnPagesString %>
    <span class="products-on-page pull-left">{$CurrentPage.productsOnPagesString}</span>
    <% end_if %>
    <ul>
    <% with $getProducts %>
        <% if $NotFirstPage %>
        <li><a href="{$PrevLink}#scpgpct" rel="prev" title="<%t SilverCart\Model\Pages\Page.PREV 'Prev' %>"><i class="icon icon-chevron-left"></i></a></li>
        <% end_if %>
        <% loop $PaginationSummary(4) %>
            <% if $CurrentBool %>
        <li class="active"><a class="highlight" href="javascript:;">{$PageNum}</a></li>
            <% else_if $Link %>
        <li><a href="{$Link}#scpgpct" title="<%t SilverCart\Model\Pages\Page.GOTO_PAGE 'go to page {count}' count=$PageNum %>">{$PageNum}</a></li>
            <% else %>
        <li><span>&hellip;</span></li>
            <% end_if %>
        <% end_loop %>

        <% if $NotLastPage %>
        <li><a href="{$NextLink}#scpgpct" rel="next" title="<%t SilverCart\Model\Pages\Page.NEXT 'Next' %>"><i class="icon icon-chevron-right"></i></a></li>
        <% end_if %>
    <% end_with %>
    </ul>
</div>
<% else_if $CurrentPage.productsOnPagesString %>
<div class="clearfix">
    <span class="products-on-page pull-left">{$CurrentPage.productsOnPagesString}</span>
</div>
<% end_if %>
