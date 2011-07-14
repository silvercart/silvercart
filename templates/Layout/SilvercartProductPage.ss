<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        <% control getProduct %>
            <div class="silvercart-product-page clearfix">
                <div class="silvercart-product-page_content">
                    
                    <div class="silvercart-product-title">
                        <h2>$Title</h2>
                        <div class="silvercart-product-meta-info">
                            <p><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</p>
                        </div>
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
                                $LongDescription
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
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
