<% if Elements %>
    <% loop Elements %>
        <div class="silvercart-product-group-page-box-list clearfix $EvenOdd $FirstLast $productAddCartFormObj.FormName">
            <div class="silvercart-product-group-page-box-list_content">
                <div class="silvercart-product-title">
                    <h3>
                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title.HTML) %>">$Title.HTML</a>
                    </h3>
                </div>
                <div class="subcolumns clearfix">
                    <div class="c25l silvercart-product-group-page-box-image">
                        <div class="subcl">
                            <% if getSilvercartImages %>
                                <% with getSilvercartImages.First %>
                                    <a href="$ProductLink" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetRatioSize(90,90)</a>
                                <% end_with %>
                            <% end_if %>
                        </div>
                    </div>
                    <div class="c45l">
                        <div class="subcl">
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
                            <div class="silvercart-button left">
                                <div class="silvercart-button_content">
                                    <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="c30r">
                        <div class="subcr">
                            <div class="silvercart-product-page-box-price">
                                <p>
                                    <strong class="silvercart-price">$Price.Nice</strong>
                                </p>
                                <p class="silvercart-price-notes">
                                    <small>
                                        <% if CurrentPage.showPricesGross %>
                                            <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                                        <% else_if CurrentPage.showPricesNet %>
                                            <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                                        <% end_if %>
                                        <% with Top.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                                            <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">
                                                <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                                            </a>
                                        <% end_with %>
                                    </small>
                                </p>
                            </div>
                            <div class="silvercart-product-availability">
                                $Availability
                            </div>
                            <% if PluggedInProductMetaData %>
                            <div class="silvercart-product-meta-data">
                                <% with PluggedInProductMetaData %>
                                    <span class="right">$MetaData</span><br/>
                                <% end_with %>
                            </div>
                            <% end_if %>
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
                <% if PluggedInProductListAdditionalData %>
                <div class="silvercart-product-list-additional-data">
                    <% control PluggedInProductListAdditionalData %>
                        $AdditionalData
                    <% end_control %>
                </div>
                <% end_if %>
            </div>
        </div>
    <% end_loop %>
<% end_if %>
