<% if Elements %>
    <% control Elements %>
        <div class="silvercart-product-group-page-box clearfix $EvenOdd">
            <div class="silvercart-product-group-page-box_content">
                <h3><a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a></h3>
                <div class="subcolumns clearfix">
                    <div class="c33l silvercart-product-group-page-box-image">
                        <div class="subcl">
                        <% if SilvercartImages %>
                            <% control SilvercartImages.First %>
                                <a href="$ProductLink" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetRatioSize(90,90)</a>
                            <% end_control %>
                        <% else %>

                        <% end_if %>
                        </div>
                    </div>
                    <div class="c66r">
                        <div class="subcr">
                            <strong><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</strong>
                            <p>$ShortDescription</p>
                            <div class="silvercart-product-page-box-price">
                                <p>
                                    <strong class="price">$Price.Nice</strong>
                                </p>
                                <p>
                                    <% if showPricesGross %>
                                        <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                                    <% else_if showPricesNet %>
                                        <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                                    <% end_if %>
                                    <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                                </p>
                                <p>
                                    <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="subcolumns clearfix">
                    <div class="c33l">
                        <div class="subcl product-status">
                            $Availability
                        </div>
                    </div>
                    <div class="c66r">
                        <div class="subcr">
                            $productAddCartForm
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <% end_control %>
<% end_if %>
