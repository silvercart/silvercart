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
                    <div class="product-group-page-details">
                        <p><strong class="price">$Price.Nice</strong><br/>
                            <% sprintf(_t('SilvercartPage.TAX', 'incl. %s%% VAT'),$tax.Rate) %><br />
                            <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                            <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                        </p>
                    </div>
                    $productAddCartForm
                </div>
            </div>
        </div>
    </div>
</div>
<% end_control %>
