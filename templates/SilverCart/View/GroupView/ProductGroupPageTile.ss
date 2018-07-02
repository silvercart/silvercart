<% if $Elements %>
<div class="row-fluid ProductGroupPageTile">
    <% if $Top.useSlider %>
    <ul class="sc-products clearfix cycle-slideshow"
            data-cycle-fx="carousel"
            data-cycle-speed="200"
            data-cycle-pause-on-hover="true"
            data-cycle-slides="> li"
            data-cycle-next="#widget-{$Top.ID} .vPrev"
            data-cycle-prev="#widget-{$Top.ID} .vNext"
            data-cycle-carousel-visible="{$numberOfProductsToShowForGroupView}"
            data-cycle-carousel-vertical="true"
        <% if not $Autoplay %>
            data-cycle-timeout="0"
        <% end_if %>
            >
    <% else %>
    <ul class="sc-products clearfix">
    <% end_if %>
        <% loop $Elements %>
        <li class="span6 silvercart-product-group-page-tile-item {$EvenOdd} clearfix <% if MultipleOf(2) %>last-in-row<% end_if %>" id="product{$ID}">
            <div class="thumbnail">
                <% if $getImages %>
                    <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title %>">$getImages.first.Image.Pad(290,290)</a>
                <% end_if %>
            </div>
            <div class="sc-product-shortinfo">
                <div class="sc-product-title">
                    <h2><a href="{$Link}" class="highlight" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title %>">{$Title.HTML}</a> {$Availability}</h2>
                </div>

                <div class="thumbPrice">

                    <span class="price">
                        <% if $PriceIsLowerThanMsr %>
                        <span class="strike-through">{$MSRPrice.Nice}</span> 
                        <% end_if %>
                        <span id="product-price-{$ID}">{$PriceNice}</span>
                    </span>

                    <% if showProductPriceAdditionalInfo %>
                    <div id="toogle{$ID}" class="collapse">
                        <small>
                            <% if $CurrentPage.showPricesGross %>
                                <%t SilverCart\Model\Pages\Page.INCLUDING_TAX 'incl. {amount}% VAT' amount=$TaxRate %>, 
                            <% else_if CurrentPage.showPricesNet %>
                                <%t SilverCart\Model\Pages\Page.EXCLUDING_TAX 'plus VAT' %>,
                            <% end_if %>

                            <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                            <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title} page' title=$Title.XML %>"><%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %></a><br/>
                            <% end_with %>                  
                        </small>       
                        <small>{$Availability}</small>
                        <p><small><%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %>: $ProductNumberShop</small></p> 
                        <% if $PackagingQuantity %>
                        <p><strong><%t SilverCart\Model\Pages\ProductPage.PACKAGING_CONTENT 'Content' %>:</strong> {$PackagingQuantity} {$QuantityUnit.Title}</p>
                        <% end_if %>
                    </div>
                    <% end_if %>
                </div>


                <div class="thumbButtons btn-toolbar">
                    <% if isBuyableDueToStockManagementSettings %>
                        {$AddToCartForm(Tile)}
                    <% else %>
                    <span class="btn btn-small btn-block btn-danger disabled pull-left"><%t SilverCart\Model\Pages\ProductPage.OUT_OF_STOCK 'This product is out of stock.' %></span>
                    <% end_if %>
                    <a class="btn btn-small pull-right" href="$Link" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title %>" data-placement="top" data-toggle="tooltip">
                        <i class="icon-info-sign"></i><%t SilverCart\Model\Pages\Page.SHOW_DETAILS 'show details' %></a>
                </div>
                <% if $showPluggedInProductMetaData && $PluggedInProductMetaData %>
                    <% loop PluggedInProductMetaData %>
                        {$MetaData}
                    <% end_loop %>
                <% end_if %> 
            </div>  
        </li>
        <% end_loop %>
    </ul>
</div>
<% end_if %>