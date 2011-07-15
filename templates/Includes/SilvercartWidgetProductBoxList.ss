<% if Elements %>
    <% control Elements %>
        <div class="silvercart-product-box">
            <div class="silvercart-product-box_content">
                <h3><a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a></h3>
                <div class="subcolumns clearfix equalize product-group-page-info">
                    <div class="c33l product-group-page-image">
                        <div class="subcl">
                            <% if getSilvercartImages %>
                                <% control getSilvercartImages.First %>
                                    <a href="$ProductLink" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$image.SetRatioSize(60,60)</a>
                                <% end_control %>
                            <% end_if %>
                        </div>
                    </div>
                    <div class="c66r">
                        <div class="subcr">
                            <p>$ShortDescription</p>

                            <div class="silvercart-product-price-details">
                                <p class="silvercart-price">
                                    <strong>$Price.Nice</strong>
                                </p>
                                <p>
                                    <small>
                                        <% if showPricesGross %>
                                            <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                                        <% else %>
                                            <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                                        <% end_if %>
                                        <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                                    </small>
                                </p>
                                <p>
                                    <small><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</small>
                                </p>
                                <div class="silvercart-button-small left">
                                    <div class="silvercart-button-small_content">
                                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <% end_control %>
<% end_if %>