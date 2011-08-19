<% if Elements %>
    <% control Elements %>
        <div class="silvercart-product-group-page-box-list clearfix $EvenOdd $FirstLast">
            <div class="silvercart-product-group-page-box-list_content">
                <div class="silvercart-product-title">
                    <h3>
                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a>
                    </h3>
                </div>
                <div class="subcolumns clearfix">
                    <div class="c25l silvercart-product-group-page-box-image">
                        <div class="subcl">
                            <% if getSilvercartImages %>
                                <% control getSilvercartImages.First %>
                                    <a href="$ProductLink" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetRatioSize(90,90)</a>
                                <% end_control %>
                            <% end_if %>
                        </div>
                    </div>
                    <div class="c45l">
                        <div class="subcl">
                            <div class="silvercart-product-text-info">
                                <p>$ShortDescription.LimitWordCountXML(35)</p>
                                <% if PackagingQuantity %>
                                <p><strong><% _t('SilvercartProductPage.PACKAGING_CONTENT') %>:</strong> $PackagingQuantity $SilvercartQuantityUnit.Name</p>
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
                                        <% if showPricesGross %>
                                            <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                                        <% else_if showPricesNet %>
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
                            <div class="silvercart-product-availability">
                                $Availability
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
        </div>
    <% end_control %>
<% end_if %>
