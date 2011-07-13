<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        <% control getProduct %>
            <div class="silvercart-product-page clearfix">
                <div class="silvercart-product-page_content">
                    
                    <div class="silvercart-product-title">
                        <h2>$Title</h2>
                    </div>
                    <div class="silvercart-product-meta-info">
                        <p><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</p>
                    </div>
                    
                    
                    <div class="subcolumns">
                        <div class="c33l silvercart-product-group-page-box-image">
                            <div class="subcl">
                                <% if getSilvercartImages %>
                                    <% control getSilvercartImages.First %>
                                        $Image.SetRatioSize(200,200)
                                    <% end_control %>
                                <% end_if %>
                            </div>
                        </div>
                        <div class="c33l">
                            <div class="subcl">
                                <div class="silvercart-product-text-info">
                                    <p>$ShortDescription</p>
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
                                            <% if showPricesGross %>
                                                <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                                            <% else_if showPricesNet %>
                                                <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                                            <% end_if %>
                                            <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                                        </small>
                                    </p>
                                </div>
                                <div class="silvercart-product-availability">
                                    $Availability
                                </div>
                                <div class="silvercart-product-group-add-cart-form">
                                    <div class="silvercart-product-group-add-cart-form_content">
                                        $productAddCartForm
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="silvercart-product-page-description">
                        <h3><% _t('SilvercartProduct.DESCRIPTION','product description') %>:</h3>
                        <p>$LongDescription</p>
                    </div>
                    <% if SilvercartFiles %>
                        <div class="silvercart-product-page-downloads">
                            <h3><% _t('SilvercartProduct.DOWNLOADS','Downloads') %>:</h3>
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
        <% end_control %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
