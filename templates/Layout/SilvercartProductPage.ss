<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <% control getProduct %>
        <div class="silvercart-product-page clearfix">
            <div class="silvercart-product-page_content">
                <h1>$Title</h1>
                <div class="subcolumns">
                    <div class="c50l">
                        <% if getSilvercartImages %>
                            <% control getSilvercartImages.First %>
                                $Image.SetRatioSize(230,190)
                            <% end_control %>
                        <% else %>

                        <% end_if %>
                    </div>
                    <div class="c50r">
                        <strong><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</strong>
                        <p>$ShortDescription</p>
                        <div class="subcolumns">
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
                        </div>
                    </div>
                </div>
                <div class="subcolumns clearfix">
                    <div class="c50l">
                        
                    </div>
                    <div class="c50r">
                        <div class="subcr">
                            $productAddCartForm
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
