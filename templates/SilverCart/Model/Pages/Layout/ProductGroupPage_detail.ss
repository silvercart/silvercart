<div class="row">
    <div class="span9" itemscope itemtype="http://schema.org/Product">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div id="sc-product-backlink" class="clearfix">
            <% if BackLink %>
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
        <% with getProduct %>
            <div class="row">
                $InsertWidgetArea(Content)
                {$BeforeProductHtmlInjections}
                <meta itemprop="productID" content="{$ProductNumberShop}" />
                <meta itemprop="url" content="{$Link}" />
                <div class="sc-product-details clearfix">
                    <div class="span5">
                        <div class="sc-product-title">
                            <h1 itemprop="name">$Title.HTML</h1>
                        </div>
                        <div class="product-img-box clearfix">
                            <div class="product-img">
                                <% if $getImages %>
                                    <% with $getImages.first %>
                                        <a itemprop="image" class="fancybox" href="$Image.Link" rel="silvercart-standard-product-image-group" >
                                            <% with Image %>
                                                <img itemprop="image" src="$Pad(372,370).URL" alt="$Title" /> 
                                            <% end_with %>
                                        </a>
                                    <% end_with %>
                                <% end_if %>
                            </div>
                            <div class="product-img-thumb">                  
                                <% if $getImages %>
                                    <% loop $getImages %>
                                        <% if First %>
                                        <% else %>
                                        <a itemprop="image" href="$Image.Link" class="fancybox" rel="silvercart-standard-product-image-group">
                                            <% with Image %>
                                             <img itemprop="image" src="$Pad(68,60).URL" alt="$Title" /> 
                                            <% end_with %>
                                        </a>
                                        <% end_if %>
                                    <% end_loop %>
                                <% end_if %> 
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="product-set">
                            <div class="product-price pull-right">
                                <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                    <meta itemprop="price" content="{$Price.Amount}" />
                                    <meta itemprop="priceCurrency" content="{$Price.Currency}" />
                                    <% if PriceIsLowerThanMsr %>
                                        <span class="strike-through">$MSRPrice.Nice</span> 
                                    <% end_if %>
                                    <strong class="price" id="product-price-{$ID}">$PriceNice</strong> 
                                </span><br/>
                                <small>
                                    <% if CurrentPage.showPricesGross %>
                                        <%t SilverCart\Model\Pages\Page.INCLUDING_TAX 'incl. {amount}% VAT' amount=$TaxRate %>
                                    <% else_if CurrentPage.showPricesNet %>
                                        <%t SilverCart\Model\Pages\Page.EXCLUDING_TAX 'plus VAT' %>
                                    <% end_if %>
                                    <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                        <a href="$Link" title="<%t SilverCart\Model\Pages\Page.GOTO 'Go to {title} page' title=$Title.XML %>">
                                            <%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %><br/>
                                        </a>
                                    <% end_with %>
                                </small>
                            </div>

                            <div class="product-rate clearfix">
                                <% if PluggedInProductMetaData %>
                                    <% with PluggedInProductMetaData %>
                                        $MetaData
                                    <% end_with %>
                                <% end_if %>            
                            </div>

                            <div class="product-info">
                                <dl class="dl-horizontal">
                                    <% if $AvailabilityStatus %>
                                        <dt><%t SilverCart\Model\Product\AvailabilityStatus.SINGULARNAME 'Availability' %>:</dt>
                                        <dd itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="availability">$Availability <meta itemprop="name" content="{$AvailabilityStatus.Title}" /></span></dd>
                                    <% end_if %>
                                    <dt><%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %>:</dt>
                                    <dd><span itemprop="model">$ProductNumberShop</span></dd>

                                     <% if PackagingQuantity %>
                                        <dt><%t SilverCart\Model\Pages\ProductPage.PACKAGING_CONTENT 'Content' %>:</dt>
                                        <dd>$PackagingQuantity $QuantityUnit.Title</dd>
                                    <% end_if %>

                                    <% if $Manufacturer %>                      
                                        <% with $Manufacturer %>
                                        <dt>{$singular_name}:</dt>
                                            <dd itemprop="brand" itemscope itemtype="http://schema.org/Brand">
                                            <% if Title %>
                                                <span itemprop="name">$Title</span>
                                            <% end_if %>
                                            <% if logo %>
                                                <% with logo %>
                                                <br/><img itemprop="logo" src="$SetRatioSize(100,50).URL" alt="$Title" /> 
                                                <% end_with %>
                                            <% end_if %>
                                            </dd>
                                        <% end_with %>        
                                    <% end_if %>
                                </dl>
                            </div>
                            <div class="product-info">
                                <p>$HtmlEncodedShortDescription</p>
                            </div>
                            <div class="product-inputs pull-right">
                            <% if isBuyableDueToStockManagementSettings %>
                                {$AddToCartForm(Detail)}
                            <% else %>
                                <%t SilverCart\Model\Pages\ProductPage.OUT_OF_STOCK 'This product is out of stock.' %>
                            <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>                           
            </div>
     
            <% include SilverCart\Model\Pages\ProductPageTabs %>
            {$AfterProductHtmlInjections}
        <% end_with %>  
    </div>

    <aside class="span3">
        <% with getProduct %>
            <% with WidgetArea %>
                <% loop WidgetControllers %>
                    $WidgetHolder
                <% end_loop %>
            <% end_with %>
        <% end_with %>
        $InsertWidgetArea(Sidebar)
    </aside>
</div>