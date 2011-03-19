<div class="product-group-page clearfix<% if Even %> even<% else %> odd<% end_if %>">
    <div class="product-group-page_content">
        <h3><a href="$Link" title="$Title.XML">$Title</a></h3>
        <div class="subcolumns clearfix">
            <div class="c33l">
                <div class="subcl">
                    <a href="$Link">$Thumbnail</a>
                </div>
            </div>
            <div class="c66r">
                <div class="subcr">
                    <p>$ShortDescription</p>
                    <div class="product-group-page-details">
                        <p><strong class="price">$Price.Nice</strong><br />
                            <% if showPricesGross %>
                            <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$SilvercartTax.Rate) %><br />
                            <% else %>
                            <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                            <% end_if %>
                            <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                            <a href="$Link" title="Details zu $Title"><% _t('SilvercartPage.DETAILS','details') %></a>
                        </p>
                    </div>
                    $productPreviewForm
                </div>
            </div>
        </div>
    </div>
</div>
