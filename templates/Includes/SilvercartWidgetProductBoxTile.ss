<% if Elements %>
<ul class="sc-products-vertical-tiny WidgetProductBoxTile">
    <% loop Elements %>
    <li class="span4 clearfix">
        <div class="thumbImage">
            <% if getSilvercartImages %>
            <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$getSilvercartImages.first.Image.SetSize(92,92)</a>
            <% end_if %>
        </div>
        <div class="sc-product-shortinfo">
            <div class="sc-product-title" id="{$ID}">
                <a class="highlight" href="{$Link}" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">{$Title.HTML}</a>
            </div>
            <div class="thumbPrice">
                <span>$PriceNice</span>
            </div>
            <% if PluggedInProductMetaData %>
            <div class="silvercart-product-meta-data">
                <% loop PluggedInProductMetaData %>
                <span>$MetaData</span>
                <% end_loop %>
            </div>
            <% end_if %>
        </div>
    </li>
    <% end_loop %>
</ul>
<% end_if %>