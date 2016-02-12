<% if Elements %>
<div class="row-fluid ProductGroupPageTile">
    <ul class="sc-products clearfix">
        <% loop Elements %>
        <li class="span6 silvercart-product-group-page-tile-item {$EvenOdd} clearfix $productAddCartFormObj.FormName <% if MultipleOf(2) %>last-in-row<% end_if %>" id="product{$ID}">
            <div class="thumbnail">
                <% if getSilvercartImages %>
                    <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$getSilvercartImages.first.Image.SetSize(290,290)</a>
                <% end_if %>
            </div>
            <div class="sc-product-shortinfo">
                <div class="sc-product-title">
                    <h2>
                        <a href="$Link" class="highlight" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title.HTML</a> {$Availability}
                    </h2>
                </div>

                <div class="thumbPrice">

                    <span class="price">
                        <% if PriceIsLowerThanMsr %>
                        <span class="strike-through">$MSRPrice.Nice</span> 
                        <% end_if %>
                        $PriceNice
                    </span>

                    <% if showProductPriceAdditionalInfo %>
                    <div id="toogle{$ID}" class="collapse">
                        <small>
                            <% if CurrentPage.showPricesGross %>

                            <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %>, 
                            <% else_if CurrentPage.showPricesNet %>
                            <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %>,
                            <% end_if %>

                            <% with $CurrentPage.PageByIdentifierCode(SilvercartShippingFeesPage) %>
                            <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">
                                <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                            </a>
                            <% end_with %>                  
                        </small>       
                        <small>
                            $Availability
                        </small>
                        <p>
                            <small><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</small>
                        </p> 
                        <% if PackagingQuantity %>
                        <p><strong><% _t('SilvercartProductPage.PACKAGING_CONTENT') %>:</strong> $PackagingQuantity $SilvercartQuantityUnit.Title</p>
                        <% end_if %>
                    </div>
                    <% end_if %>
                </div>


                <div class="thumbButtons btn-toolbar">
                    <% if isBuyableDueToStockManagementSettings %>
                    $productAddCartForm
                    <% else %>
                    <span class="btn btn-small btn-block btn-danger disabled pull-left"><% _t('SilvercartProductPage.OUT_OF_STOCK') %></span>
                    <% end_if %>
                    <a class="btn btn-small pull-right" href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>" data-title="<% _t('SilvercartPage.SHOW_DETAILS','show details') %>" data-placement="top" data-toggle="tooltip">
                        <i class="icon-info-sign"></i><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                </div>
                <% if showPluggedInProductMetaData  %>
                <!-- todo metadata must be in star format -->
                <% if PluggedInProductMetaData %>
                <% loop PluggedInProductMetaData %>
                $MetaData
                <% end_loop %>
                <% end_if %> 
                <% end_if %> 
            </div>  
        </li>

        <% end_loop %>
    </ul>
</div>
<% end_if %>