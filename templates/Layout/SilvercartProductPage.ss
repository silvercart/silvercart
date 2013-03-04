<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>
        
        $InsertWidgetArea(Content)
        
        <div class="silvercart-product-actions clearfix">
            <a class="silvercart-icon-with-text-button back16 left" href="$BackLink">
                <span class="silvercart-icon-with-text-button_content">
                    <% sprintf(_t('SilvercartPage.BACK_TO'),$BackPage.MenuTitle) %>
                </span>
            </a>
            <a class="silvercart-icon-button print16 left" href="javascript:window.print()" title="<% _t('Silvercart.PRINT') %>">
                <span class="silvercart-icon-button_content">
                    &nbsp;
                </span>
            </a>
            <a class="silvercart-icon-button help16 left" href="{$getProduct.ProductQuestionLink}" title="<% _t('SilvercartProduct.PRODUCT_QUESTION_LABEL') %>">
                <span class="silvercart-icon-button_content">
                    &nbsp;
                </span>
            </a>
        </div>
        
        <% control getProduct %>
            {$BeforeProductHtmlInjections}
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
                                    <p>$HtmlEncodedShortDescription</p>
                                    <% if PackagingQuantity %>
                                    <p><strong><% _t('SilvercartProductPage.PACKAGING_CONTENT') %>:</strong> $PackagingQuantity $SilvercartQuantityUnit.Title</p>
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
                                <% if PluggedInProductMetaData %>
                                <div class="silvercart-product-meta-data">
                                    <% control PluggedInProductMetaData %>
                                        $MetaData<br/>
                                    <% end_control %>
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
                    
                    <div class="silvercart-product-page-product-info">
                        <ul class="tabs">
                            <li rel="product_description">
                                <a href="#product_description"><% _t('SilvercartProduct.DESCRIPTION','product description') %></a>
                            </li>
                            <% if SilvercartFiles %>
                                <li rel="downloads">
                                    <a href="#downloads"><% _t('SilvercartProduct.DOWNLOADS','Downloads') %></a>
                                </li>
                            <% end_if %>
                            <% if PluggedInTabs %>
                                <% control PluggedInTabs %>
                                <li rel="<% if TabID %>$TabID<% else %>pluggedInTab{$Pos}<% end_if %>">
                                    <a href="#<% if TabID %>$TabID<% else %>pluggedInTab{$Pos}<% end_if %>">$Name</a>
                                </li>
                                <% end_control %>
                            <% end_if %>
                        </ul>
                        <div class="tab_container">
                            <div id="product_description" class="tab_content">
                                $HtmlEncodedLongDescription
                            </div>
                            <% if SilvercartFiles %>
                                <div id="downloads" class="tab_content">
                                    <table class="full silvercart-default-table">
                                        <colgroup>
                                            <col width="20%"></col>
                                            <col width="65%"></col>
                                            <col width="15%"></col>
                                        </colgroup>
                                        <tr>
                                            <th><% _t('SilvercartFile.TYPE') %></th>
                                            <th><% _t('SilvercartFile.TITLE') %></th>
                                            <th class="align_right"><% _t('SilvercartFile.SIZE') %></th>
                                        </tr>
                                        <% control SilvercartFiles %>
                                            <tr class="$EvenOdd">
                                                <td>
                                                    <div class="silvercart-file-icon">
                                                        <a href="$File.Link">$FileIcon</a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="$File.Link">$Title</a>
                                                </td>
                                                <td class="align_right">
                                                    <a href="$File.Link">$File.Size</a>
                                                </td>
                                            </tr>
                                        <% end_control %>
                                    </table>
                                </div>
                            <% end_if %>
                            <% if getPluggedInTabs %>
                                <% control PluggedInTabs %>
                                <div id="<% if TabID %>$TabID<% else %>pluggedInTab{$Pos}<% end_if %>" class="tab_content">$Content</div>
                                <% end_control %>
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
        <% control getProduct %>
            <% control WidgetArea %>
                <% control WidgetControllers %>
                    $WidgetHolder
                <% end_control %>
            <% end_control %>
        <% end_control %>
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
