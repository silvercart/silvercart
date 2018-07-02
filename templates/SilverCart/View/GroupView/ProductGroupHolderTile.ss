<% if $Elements %>
<div class="row ProductGroupHolderTile">
    <ul class="sc-products clearfix">
    <% loop $Elements %>
        <li class="span4 clearfix">
            <div class="sc-product-title">
                <h2 id="{$ID}">
                    <a href="{$Link}" class="highlight" title="{$Title}">{$MenuTitle.HTML}</a>
                </h2>
            </div>
            <div class="thumbnail">
                <% if groupPicture %>
                    <a href="$Link" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Image.Title %>">
                        $groupPicture.Pad(290,290)
                    </a>
                <% end_if %>
            </div>
            <div class="sc-product-shortinfo">                       
                <div class="product-info">
                    <p>{$Content.NoHTML.LimitWordCount(12)}</p>
                </div>
                <div class="thumbButtons">
                    <% if $hasProductCount(1) %> 
                        <a href="{$Link}" class="btn btn-primary btn-small" title="<%t SilverCart\Model\Pages\ProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_SINGLUAR 'Show {count} product' count=$ActiveProducts.Count %>" data-placement="top" data-toggle="tooltip"><%t SilverCart\Model\Pages\ProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_SINGLUAR 'Show {count} product' count=$ActiveProducts.Count %> <span class="icon-caret-right"></span></a>
                    <% else_if not $hasProductCount(0) %>
                        <a href="{$Link}" class="btn btn-primary btn-small" title="<%t SilverCart\Model\Pages\ProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_PLURAL 'Show {count} products' count=$ActiveProducts.Count %>" data-placement="top" data-toggle="tooltip"><%t SilverCart\Model\Pages\ProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_PLURAL 'Show {count} products' count=$ActiveProducts.Count %> <span class="icon-caret-right"></span></a>
                    <% end_if %>
                </div>
            </div>
        </li>
    <% end_loop %>
    </ul>
</div>
<% end_if %>
