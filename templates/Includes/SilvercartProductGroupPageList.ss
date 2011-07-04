<% if Elements %>
    <% control Elements %>
        <div class="silvercart-product-group-page-box clearfix $EvenOdd $FirstLast">
            <div class="silvercart-product-group-page-box_content">
                <h3><a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a></h3>
                <div class="subcolumns clearfix">
                    <div class="c25l silvercart-product-group-page-box-image">
                        <div class="subcl">
                            <% if getSilvercartImages %>
                                <% control getSilvercartImages.First %>
                                    TITEL: $Image.Title
                                    <a href="$ProductLink" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetRatioSize(90,90)</a>
                                <% end_control %>
                            <% end_if %>
                        </div>
                    </div>
                    <div class="c50l">
                        <div class="subcl">
                            <p>
                                <small><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</small>
                            </p>
                            <p>$ShortDescription</p>
                            <div class="silvercart-product-page-box-price">
                                <p>
                                    <strong class="price">$Price.Nice</strong>
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
                        </div>
                    </div>
                    <div class="c25r">
                        <div class="subcr">
                            $Availability
                            $productAddCartForm
                            <p>
                                <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <% end_control %>
<% end_if %>
