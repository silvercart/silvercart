<div class="silvercart-product-box">
    <div class="silvercart-product-box_content">
        <h3><a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a></h3>
        <div class="subcolumns clearfix equalize product-group-page-info">
            <div class="c33l product-group-page-image">
                <div class="subcl">
                    <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$image.SetRatioSize(90,90)</a>
                </div>
            </div>
            <div class="c66r">
                <div class="subcr">
                    <strong><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $ProductNumberShop</strong>
                    <p>$ShortDescription</p>
                </div>
            </div>
        </div>
        <div class="product-group-page-details">
            <p><strong class="price">$Price.Nice</strong><br/>
                <% if showPricesGross %>
                    <% sprintf(_t('SilvercartPage.INCLUDING_TAX', 'incl. %s%% VAT'),$TaxRate) %><br />
                    <% else %>
                    <% _t('SilvercartPage.EXCLUDING_TAX', 'plus VAT') %><br />
                    <% end_if %>
                <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
            </p>
        </div>
    </div>
</div>