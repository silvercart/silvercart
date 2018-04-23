<% if $Elements %>
        <% loop $Elements %>
        <div class="silvercart-product-group-page-list-item {$EvenOdd} pull-left">
            <div class="row-fluid {$EvenOdd} {$FirstLast} li" id="product{$ID}">
                <div class="thumbnail span4 pull-left">
                <% if $ListImage %>
                    <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title %>"><img class="img-fluid" src="{$ListImage.Pad(214,214).URL}" alt="{$Title}" /></a>
                <% end_if %>
                </div>

                <div class="sc-product-shortinfo span8 pull-left padding_left">
                    <div class="sc-product-title">
                        <h2>
                            <a href="{$Link}" class="highlight" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title.HTML %>">{$Title.HTML}</a> {$Availability}
                        </h2>
                        <%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %>: {$ProductNumberShop}
                    </div>  
                    
                   
                    <div class="sc-product-price">
                        <div class="price">
                            <% if PriceIsLowerThanMsr %>
                                <span class="strike-through">$MSRPrice.Nice</span> 
                            <% end_if %>
                            <span id="product-price-{$ID}">{$PriceNice}</span>
                        </div>
                    </div>
                    <div class="sc-product-price-info">
                        <small>
                            <% if CurrentPage.showPricesGross %>
                                <%t SilverCart\Model\Pages\Page.INCLUDING_TAX 'incl. {amount}% VAT' amount=$TaxRate %>
                            <% else_if CurrentPage.showPricesNet %>
                                <%t SilverCart\Model\Pages\Page.EXCLUDING_TAX 'plus VAT' %>
                            <% end_if %>

                            <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                <a href="$Link" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title} page' title=$Title.XML %>">
                                    <%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %>
                                </a>
                            <% end_with %>
                            <% if PackagingQuantity %>
                            | <strong><%t SilverCart\Model\Pages\ProductPage.PACKAGING_CONTENT 'Content' %>:</strong> $PackagingQuantity $QuantityUnit.Title
                            <% end_if %>
                        </small>
                    </div>
                    <% if PluggedInProductMetaData %>
                        <div>
                            <hr>
                            <% loop PluggedInProductMetaData %>
                                $MetaData  
                            <% end_loop %>
                        </div>
                    <% end_if %>
                    
                    <% if getHtmlEncodedShortDescription %>
                    <div class="sc-product-description">
                        <p>$getHtmlEncodedShortDescription</p>
                    </div>
                    <% end_if %>
                        <div class="row-fluid">
                            <div class="span6">
                                <% if isBuyableDueToStockManagementSettings %>
                                    {$AddToCartForm(List)}
                                <% else %>
                                    <span class="btn btn-small btn-danger disabled pull-left"><%t SilverCart\Model\Pages\ProductPage.OUT_OF_STOCK 'This product is out of stock.' %></span>
                                <% end_if %>   
                            </div>
                            <div class="span6">
                                <a class="btn btn-small pull-right" data-placement="top" data-toggle="tooltip" href="$Link" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title %>"><i class="icon-info-sign"></i> <%t SilverCart\Model\Pages\Page.SHOW_DETAILS 'show details' %></a>
                            </div>

                        </div>

                        <% if PluggedInProductListAdditionalData %>
                            <div class="pull-left">
                                <% with PluggedInProductListAdditionalData %>
                                $AdditionalData
                                <% end_with %>
                            </div>
                        <% end_if %>

                </div>  
            </div>
        </div>
        <% end_loop %>
<% end_if %>
