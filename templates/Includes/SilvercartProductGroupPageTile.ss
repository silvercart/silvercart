<% if Elements %>
    <% control Elements %>
        <% if MultipleOf(2) %>
            <div class="c50r silvercart-product-group-page-box-tile tile $EvenOdd $productAddCartFormObj.FormName">
        <% else %>
            <div class="subcolumns equalize clearfix">
                <div class="c50l silvercart-product-group-page-box-tile tile $EvenOdd $productAddCartFormObj.FormName">
        <% end_if %>
            <div class="silvercart-product-group-page-box-tile_content">
                <div class="silvercart-product-group-page-box-tile_frame">
                    <div class="silvercart-product-title">
                        <h3>
                            <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title.HTML</a>
                        </h3>
                    </div>
                    <div class="subcolumns clearfix equalize product-group-page-info">
                        <div class="c33l product-group-page-image">
                            <div class="subcl">
                                <% if getSilvercartImages %>
                                    <% control getSilvercartImages.First %>
                                        <a href="$ProductLink" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetRatioSize(90,90)</a>
                                    <% end_control %>
                                <% end_if %>
                                <div class="silvercart-product-page-box-price">
                                    <p>
                                        <strong class="silvercart-price">$PriceNice</strong>
                                    </p>
                                    <p class="silvercart-price-notes">
                                        <small>
                                            <% if CurrentPage.showPricesGross %>
                                                <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                                            <% else_if CurrentPage.showPricesNet %>
                                                <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                                            <% end_if %>
                                            <% control Top.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                                <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">
                                                    <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                                                </a>
                                            <% end_control %>
                                        </small>
                                    </p>
                                </div>
                                <div class="silvercart-product-availability right">
                                    $Availability
                                </div>
                                <% if PluggedInProductMetaData %>
                                <div class="silvercart-product-meta-data">
                                    <% control PluggedInProductMetaData %>
                                        <span class="right">$MetaData</span><br/>
                                    <% end_control %>
                                </div>
                                <% end_if %>
                            </div>
                        </div>
                        <div class="c66r">
                            <div class="subcr">
                                <div class="silvercart-product-text-info">
                                    <p>$getHtmlEncodedShortDescription</p>
                                    <% if PackagingQuantity %>
                                    <p><strong><% _t('SilvercartProductPage.PACKAGING_CONTENT') %>:</strong> $PackagingQuantity $SilvercartQuantityUnit.Title</p>
                                    <% end_if %>
                                </div>
                                <div class="silvercart-product-meta-info">
                                    <p>
                                        <small><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</small>
                                    </p>
                                </div>  
                            </div>
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="silvercart-button left">
                            <div class="silvercart-button_content">
                                <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                            </div>
                        </div>

                        <div class="silvercart-product-group-add-cart-form">
                            <div class="silvercart-product-group-add-cart-form_content">
                                <% if isBuyableDueToStockManagementSettings %>
                                    $productAddCartForm
                                <% else %>
                                    <% _t('SilvercartProductPage.OUT_OF_STOCK') %>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <% if MultipleOf(2) %>
            </div>
        <% else_if Last %>
            </div>
        <% end_if %>
    <% end_control %>
<% end_if %>