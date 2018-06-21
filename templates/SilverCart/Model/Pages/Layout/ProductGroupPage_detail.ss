<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div id="sc-product-backlink" class="clearfix">
            <% if $BackLink %>
                <a class="btn btn-small pull-left" href="{$BackLink}#product{$getProduct.ID}">
                    <i class="icon-chevron-left"></i>
                    <%t SilverCart\Model\Pages\Page.BACK_TO 'Back to &quot;{title}&quot;' title=$BackPage.MenuTitle %>
                </a>
            <% end_if %>
            <a class="btn btn-small pull-left" href="javascript:window.print()" title="<%t SilverCart\Model\Pages\ProductPage.PRINT 'Print' %>">
                <i class="icon-print"></i>
            </a>
            <a class="btn btn-small pull-left" href="{$getProduct.ProductQuestionLink}" title="<%t SilverCart\Model\Product\Product.PRODUCT_QUESTION_LABEL 'Questions for the product' %>">
                <i class="icon-envelope"></i>
            </a>
        </div>
        <% with $getProduct %>
            <div class="row">
                {$InsertWidgetArea(Content)}
                {$BeforeProductHtmlInjections}
                <div class="sc-product-details clearfix">
                    <div class="span5">
                        <div class="sc-product-title">
                            <h1>{$Title.HTML}</h1>
                        </div>
                        <div class="product-img-box clearfix">
                            <div class="product-img">
                                <% if $ListImage %>
                                    <% with $ListImage %>
                                        <a class="fancybox" href="{$Link}" rel="silvercart-standard-product-image-group"><img src="{$Pad(372,370).URL}" alt="{$Up.Title}" /></a>
                                    <% end_with %>
                                <% end_if %>
                            </div>
                            <div class="product-img-thumb">
                                <% if $getImages %>
                                    <% loop $getImages %>
                                        <% if not $First %>
                                        <a href="{$Image.Link}" class="fancybox" rel="silvercart-standard-product-image-group">
                                            <img src="{$Image.Pad(68,60).URL}" alt="{$Title}" />
                                        </a>
                                        <% end_if %>
                                    <% end_loop %>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="product-set">
                            <div class="product-price pull-right text-right">
                                <% if $PriceIsLowerThanMsr %>
                                    <span class="strike-through">{$MSRPrice.Nice}</span>
                                <% end_if %>
                                <strong class="price" id="product-price-{$ID}">{$PriceNice}</strong><br/>
                                <small>
                                    <% if $CurrentPage.showPricesGross %>
                                        <%t SilverCart\Model\Pages\Page.INCLUDING_TAX 'incl. {amount}% VAT' amount=$TaxRate %>
                                    <% else_if $CurrentPage.showPricesNet %>
                                        <%t SilverCart\Model\Pages\Page.EXCLUDING_TAX 'plus VAT' %>
                                    <% end_if %>
                                    <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                        <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO 'Go to {title} page' title=$Title.XML %>">
                                            <%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %><br/>
                                        </a>
                                    <% end_with %>
                                </small>
                            </div>

                            <span class="clearfix"></span>
                            <div class="product-info">
                                <% if $PluggedInProductMetaData %>
                                    <% loop $PluggedInProductMetaData %>
                                        {$MetaData}
                                    <% end_loop %>
                                <% end_if %>
                                <dl class="dl-horizontal">
                                    <% if $AvailabilityStatus %>
                                        <dt><%t SilverCart\Model\Product\AvailabilityStatus.SINGULARNAME 'Availability' %>:</dt>
                                        <dd>{$Availability}</dd>
                                    <% end_if %>
                                    <dt><%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %>:</dt>
                                    <dd>{$ProductNumberShop}</dd>
                                    <%-- if $Top.SiteConfig.enableStockManagement %>
                                        <dt>{$fieldLabel(StockQuantity)}:</dt>
                                        <dd><span>{$StockQuantity} {$QuantityUnit.Title}</span></dd>
                                    <% end_if --%>
                                    <% if $PackagingQuantity %>
                                        <dt><%t SilverCart\Model\Pages\ProductPage.PACKAGING_CONTENT 'Content' %>:</dt>
                                        <dd>{$PackagingQuantity} {$QuantityUnit.Title}</dd>
                                    <% end_if %>
                                    <% if $Manufacturer %>
                                        <% with $Manufacturer %>
                                        <dt>{$singular_name}:</dt>
                                        <dd><% if $Title %>{$Title}<% end_if %>
                                            <% if $logo %><br/><img src="{$logo.Pad(100,50).URL}" alt="{$Title}" /><% end_if %>
                                            </dd>
                                        <% end_with %>
                                    <% end_if %>
                                </dl>
                            </div>
                            <div class="product-info">
                                <p>{$HtmlEncodedShortDescription}</p>
                            </div>
                            <div class="product-inputs pull-right">
                            <% if $isBuyableDueToStockManagementSettings %>
                                {$AddToCartForm(Detail)}
                            <% else %>
                                <%t SilverCart\Model\Pages\ProductPage.OUT_OF_STOCK 'This product is out of stock.' %>
                            <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <% if $PluggedInAfterImageContent %>
                <% loop $PluggedInAfterImageContent %>
                    {$Content}
                <% end_loop %>
            <% end_if %>
            <% include SilverCart\Model\Pages\ProductPageTabs %>
            {$AfterProductHtmlInjections}
        <% end_with %>
    </div>

    <aside class="span3">
        <% if $getProduct.WidgetArea.WidgetControllers %>
            <% loop $getProduct.WidgetArea.WidgetControllers %>
                {$WidgetHolder}
            <% end_loop %>
        <% end_if %>
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>