<% if Elements %>
        <% loop Elements %>
        <div class="silvercart-product-group-page-list-item {$EvenOdd} pull-left">
            <div class="row-fluid $EvenOdd $FirstLast $productAddCartFormObj.FormName li" id="product{$ID}">
                <div class="thumbnail span4 pull-left">
                    <% if getSilvercartImages %>
                    <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$getSilvercartImages.first.Image.SetSize(214,214)</a>
                    <% end_if %>
                </div>

                <div class="sc-product-shortinfo span8 pull-left padding_left">
                    <div class="sc-product-title">
                        <h2>
                            <a href="$Link" class="highlight" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title.HTML) %>">$Title.HTML</a> {$Availability}
                        </h2>
                        <% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop
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
                                <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %>
                            <% else_if CurrentPage.showPricesNet %>
                                <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %>
                            <% end_if %>

                            <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">
                                    <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %>
                                </a>
                            <% end_with %>
                            <% if PackagingQuantity %>
                            | <strong><% _t('SilvercartProductPage.PACKAGING_CONTENT') %>:</strong> $PackagingQuantity $SilvercartQuantityUnit.Title
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
                                    $productAddCartForm
                                <% else %>
                                    <span class="btn btn-small btn-danger disabled pull-left"><% _t('SilvercartProductPage.OUT_OF_STOCK') %></span>
                                <% end_if %>   
                            </div>
                            <div class="span6">
                                <a class="btn btn-small pull-right" data-title="<% _t('SilvercartPage.SHOW_DETAILS','show details') %>" data-placement="top" data-toggle="tooltip" href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><i class="icon-info-sign"></i> <% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
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
