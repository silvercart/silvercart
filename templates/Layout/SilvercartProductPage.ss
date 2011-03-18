<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        <% control getProduct %>
        <div class="product-page clearfix">
            <div class="product-page_content">
                <h1>$Title</h1>
                <div class="subcolumns">
                    <div class="c50l">
                        $image.SetRatioSize(230,190)
                    </div>
                    <div class="c50r">
                        <p>$ShortDescription</p>
                        <div class="subcolumns">
                            <p class="product-page-details"><strong class="price">$Price.Nice</strong><br/>
                                <% sprintf(_t('SilvercartPage.TAX', 'incl. %s%% VAT'),$SilvercartTax.Rate) %><br />
                                <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="subcolumns clearfix">
                    <div class="c50l">
                        <div class="subcl product-status">
                            $Availability
                        </div>
                    </div>
                    <div class="c50r">
                        <div class="subcr">
                            $productAddCartForm
                        </div>
                    </div>
                </div>
                <div class="product-page-description">
                    <h3><% _t('SilvercartProduct.DESCRIPTION','product description') %>:</h3>
                    <p>$LongDescription</p>
                </div>
            </div>
        </div>
        <% end_control %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SilvercartSideBarCart %>
        $SubNavigation
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>