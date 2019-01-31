<div class="row">
    <div class="span9" itemscope itemtype="http://schema.org/Product">
        <% include SilvercartBreadCrumbs %>
        <div id="sc-product-backlink" class="clearfix">
            <% if BackLink %>
                <a class="btn btn-small pull-left" href="{$BackLink}#product{$getProduct.ID}">
                    <i class="icon-chevron-left"></i>
                    <% sprintf(_t('SilvercartPage.BACK_TO'),$BackPage.MenuTitle) %>
                </a>
            <% end_if %>
            <a class="btn btn-small pull-left" href="javascript:window.print()" title="<%t Silvercart.PRINT 'Print' %>">
                <i class="icon-print"></i>
            </a>
            <a class="btn btn-small pull-left" href="{$getProduct.ProductQuestionLink}" title="<%t SilvercartProduct.PRODUCT_QUESTION_LABEL '' %>">
                <i class="icon-envelope"></i>
            </a>
        </div>
        <% with $getProduct %>
            <div class="row">
                {$InsertWidgetArea('Content')}
                {$BeforeProductHtmlInjections}
                <meta itemprop="productID" content="{$ProductNumberShop}" />
                <meta itemprop="url" content="{$Link}" />
                <div class="sc-product-details clearfix">
                    <div class="span5">
                        <div class="sc-product-title">
                            <h1 itemprop="name">{$Title.HTML}</h1>
                        </div>
                        <div class="product-img-box clearfix">
                            <div class="product-img">
                                <% if $getSilvercartImages %>
                                    <% with $getSilvercartImages.First %>
                                        <a itemprop="image" class="fancybox" href="{$Image.Link}" rel="silvercart-standard-product-image-group" >
                                            <% with $Image %>
                                                <img itemprop="image" src="{$SetSize(372,370).URL}" alt="{$Title}" /> 
                                            <% end_with %>
                                        </a>
                                    <% end_with %>
                                <% end_if %>
                            </div>
                            <div class="product-img-thumb">                  
                                <% if $getSilvercartImages %>
                                    <% loop $getSilvercartImages %>
                                        <% if not $First %>
                                        <a itemprop="image" href="{$Image.Link}" class="fancybox" rel="silvercart-standard-product-image-group">
                                            <% with $Image %>
                                             <img itemprop="image" src="{$SetSize(68,60).URL}" alt="{$Title}" /> 
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
                        <% if $canViewPrice %>
                            <div class="product-price pull-right">
                                <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                    <meta itemprop="price" content="{$Price.Amount}" />
                                    <meta itemprop="priceCurrency" content="{$Price.Currency}" />
                                    <% if $PriceIsLowerThanMsr %>
                                        <span class="strike-through">{$MSRPrice.Nice}</span> 
                                    <% end_if %>
                                    <strong class="price" id="product-price-{$ID}">{$PriceNice}</strong> 
                                </span><br/>
                                <small>
                                    <% if $CurrentPage.showPricesGross %>
                                        <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %>
                                    <% else_if $CurrentPage.showPricesNet %>
                                        <%t SilvercartPage.EXCLUDING_TAX 'plus VAT' %>
                                    <% end_if %>
                                    <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                        <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">
                                            <%t SilvercartPage.PLUS_SHIPPING 'plus shipping' %><br/>
                                        </a>
                                    <% end_with %>
                                </small>
                            </div>
                            <span class="clearfix"></span>
                        <% end_if %>
                            <div class="product-info">
                                <% if $PluggedInProductMetaData %>
                                    <% loop $PluggedInProductMetaData %>{$MetaData}<% end_loop %>
                                <% end_if %>
                                <dl class="dl-horizontal">
                                    <% if $SilvercartAvailabilityStatus %>
                                        <dt><%t SilvercartAvailabilityStatus.SINGULARNAME '' %>:</dt>
                                        <dd itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="availability">{$Availability} <meta itemprop="name" content="{$SilvercartAvailabilityStatus.Title}" /></span></dd>
                                    <% end_if %>
                                    <dt><%t SilvercartProduct.PRODUCTNUMBER_SHORT '' %>:</dt>
                                    <dd><span itemprop="model">{$ProductNumberShop}</span></dd>
                                     <% if $PackagingQuantity %>
                                        <dt><%t SilvercartProductPage.PACKAGING_CONTENT '' %>:</dt>
                                        <dd>{$PackagingQuantity} {$SilvercartQuantityUnit.Title}</dd>
                                    <% end_if %>
                                    <% if $SilvercartManufacturer %>                      
                                        <dt><%t SilvercartManufacturer.SINGULARNAME '' %>:</dt>
                                        <% with $SilvercartManufacturer %>
                                            <dd itemprop="brand" itemscope itemtype="http://schema.org/Brand">
                                            <% if $Title %>
                                                <span itemprop="name">{$Title}</span>
                                            <% end_if %>
                                            <% if $logo %>
                                                <% with $logo %>
                                                <br/><img itemprop="logo" src="{$SetRatioSize(100,50).URL}" alt="{$Title}" /> 
                                                <% end_with %>
                                            <% end_if %>
                                            </dd>
                                        <% end_with %>        
                                    <% end_if %>
                                </dl>
                            </div>
                            <div class="product-info">
                                <p>{$HtmlEncodedShortDescription}</p>
                            </div>
                            <div class="product-inputs pull-right">
                        <% if $canBuy %>
                            <% if isBuyableDueToStockManagementSettings %>
                                {$productAddCartForm}
                            <% else %>
                                <%t SilvercartProductPage.OUT_OF_STOCK '' %>
                            <% end_if %>
                        <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>                           
            </div>
            <% include SilvercartProductPageTabs %>
            {$AfterProductHtmlInjections}
        <% end_with %>  
    </div>
    <aside class="span3">
        <% loop $getProduct.WidgetArea.WidgetControllers %>
            {$WidgetHolder}
        <% end_loop %>
        {$InsertWidgetArea('Sidebar')}
    </aside>
</div>