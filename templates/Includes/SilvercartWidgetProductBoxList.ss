<% if Elements %>
        <% if Top.useSlider %>
    <ul class="sc-products-vertical vertical clearfix cycle-slideshow"
            data-cycle-fx="carousel"
            data-cycle-speed="200"
            data-cycle-pause-on-hover="true"
            data-cycle-slides="> li"
            data-cycle-next="#widget-{$ID} .vPrev"
            data-cycle-prev="#widget-{$ID} .vNext"
            data-cycle-carousel-visible="{$numberOfProductsToShow}"
            data-cycle-carousel-vertical="true"
            <% if Autoplay %>
            <% else %>
            data-cycle-timeout="0"
            <% end_if %>
            >
        <% else %>
    <ul class="sc-products-vertical vertical clearfix">
        <% end_if %>
        <% loop Elements %>
        <li class="span4 clearfix">
            <div class="thumbImage">
                <% if getSilvercartImages %>
                    <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$getSilvercartImages.first.Image.SetSize(92,92)</a>
                <% end_if %>
            </div>
            <div class="sc-product-shortinfo">
                <div class="sc-product-title">
                    <a class="highlight" href="{$Link}" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title.LimitWordCount(3)</a>
                </div>
                <div class="thumbPrice">
                    <span>$PriceNice</span>
                </div>
                <% if PluggedInProductMetaData %>
                    <% loop PluggedInProductMetaData %>
                    $MetaData
                    <% end_loop %>
                <% end_if %>
            </div>
        </li>
        <% end_loop %>
    </ul>

<% end_if %>