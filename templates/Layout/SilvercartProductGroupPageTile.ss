<% if Elements %>
    <% control Elements %>
        <% if MultipleOf(2) %>
<div class="c50r product-group-page tile $EvenOdd">
        <% else %>
    <div class="subcolumns equalize clearfix">
        <div class="c50l product-group-page tile $EvenOdd">
        <% end_if %>

            <div class="product-group-page_content">
                <h3><a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$Title</a></h3>
                <div class="subcolumns clearfix equalize product-group-page-info">
                    <div class="c33l product-group-page-image">
                        <div class="subcl">
                            <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>">$image.SetRatioSize(90,90)</a>
                        </div>
                    </div>
                    <div class="c66r">
                        <div class="subcr">
                            <p>$ShortDescription</p>
                        </div>
                    </div>
                </div>
                <div class="product-group-page-details">
                    <p><strong class="price">$Price.Nice</strong><br/>
                        <% sprintf(_t('SilvercartPage.TAX', 'incl. %s%% VAT'),$SilvercartTax.Rate) %><br />
                        <% _t('SilvercartPage.PLUS_SHIPPING','plus shipping') %><br/>
                        <a href="$Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Title) %>"><% _t('SilvercartPage.SHOW_DETAILS','show details') %></a>
                    </p>
                </div>
                $productAddCartForm
            </div>
        </div>
        <% if MultipleOf(2) %>
    </div>
        <% else_if Last %>
</div>
        <% end_if %>
    <% end_control %>
<% end_if %>
