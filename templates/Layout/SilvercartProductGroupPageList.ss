<% if Elements %>
    <% control Elements %>
<div class="product-group-page clearfix $EvenOdd">
    <div class="product-group-page_content">
        <h3><a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a></h3>
        <div class="subcolumns clearfix">
            <div class="c33l product-group-page-image">
                <div class="subcl">
                    <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$image.SetRatioSize(150,150)</a>
                </div>
            </div>
            <div class="c66r">
                <div class="subcr">
                    <p>$ShortDescription</p>
                    <div class="product-page-details">
                        <p><strong class="price">$Price.Nice</strong><br/>
                            <% if showPricesGross %>
                            <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$SilvercartTax.Rate) %><br />
                            <% else_if showPricesNet %>
                            <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                            <% end_if %>
                            <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
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
