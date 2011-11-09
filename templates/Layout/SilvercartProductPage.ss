<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        $InsertWidgetArea(Content)
        
        <% control getProduct %>
            <div class="silvercart-product-page clearfix">
                <div class="silvercart-product-page_content">
                    
                    <div class="silvercart-product-title">
                        <h2>$Title.HTML</h2>
                        <div class="silvercart-product-meta-info">
                            <p><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</p>
                        </div>
                    </div>
                    
                    <div class="subcolumns">
                        <div class="c33l silvercart-product-page-box-images">
                            <div class="subcl">
                                <% if getSilvercartImages %>
                                    <% control getSilvercartImages.First %>
                                        <div class="silvercart-product-page-box-image">
                                            <a href="$Image.Link" class="silvercart-product-detail-image" rel="silvercart-standard-product-image-group">
                                                $Image.SetRatioSize(200,200)
                                            </a>
                                        </div>
                                    <% end_control %>
                                <% end_if %>
                                
                                <div class="silvercart-product-image-list">
                                    <% if getSilvercartImages %>
                                        <% control getSilvercartImages %>
                                            <% if First %>
                                            <% else %>
                                                <div class="silvercart-product-image-list-entry">
                                                    <div class="silvercart-product-image-list-entry_content">
                                                        <a href="$Image.Link" class="silvercart-product-detail-image" rel="silvercart-standard-product-image-group">
                                                            $Image.SetRatioSize(90,90)
                                                        </a>
                                                    </div>
                                                </div>
                                            <% end_if %>
                                        <% end_control %>
                                    <% end_if %>
                                </div>
                            </div>
                        </div>
                        <div class="c33l">
                            <div class="subcl">
                                <div class="silvercart-product-text-info">
                                    <p>$ShortDescription.HTML</p>
                                    <% if PackagingQuantity %>
                                    <p><strong><% _t('SilvercartProductPage.PACKAGING_CONTENT') %>:</strong> $PackagingQuantity $SilvercartQuantityUnit.Name</p>
                                    <% end_if %>
                                </div>
                            </div>
                        </div>
                        <div class="c33r">
                            <div class="subcr">
                                <div class="silvercart-product-page-box-price">
                                    <p>
                                        <strong class="silvercart-price">$Price.Nice</strong>
                                    </p>
                                    <p>
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
                    
                    <div class="silvercart-product-page-product-info">
                        <ul class="tabs">
                            <li>
                                <a href="#tab1"><% _t('SilvercartProduct.DESCRIPTION','product description') %></a>
                            </li>
                            <% if SilvercartFiles %>
                                <li>
                                    <a href="#tab2"><% _t('SilvercartProduct.DOWNLOADS','Downloads') %></a>
                                </li>
                            <% end_if %>
                        </ul>
                        <div class="tab_container">
                            <div id="tab1" class="tab_content">
                                $HtmlEncodedLongDescription
                            </div>
                            <% if SilvercartFiles %>
                                <div id="tab2" class="tab_content">
                                    <% control SilvercartFiles %>
                                        <div class="silvercart-product-page-downloads-entry clearfix">
                                            <div class="silvercart-file-icon">
                                                <a href="$File.Link">$FileIcon</a>
                                            </div>
                                            <div class="silvercart-file-description">
                                                <a href="$File.Link">$Title ($File.Size)</a>
                                            </div>
                                        </div>
                                    <% end_control %>
                                </div>
                            <% end_if %>
                        </div>
                    </div>
                    
                </div>
            </div>
        <% end_control %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
