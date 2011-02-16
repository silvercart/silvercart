<% control Elements %>
<div class="product-group-page clearfix $EvenOdd">
    <div class="product-group-page_content">
        <h3><a href="$Link" title="$Title.XML">$Title</a></h3>
        <div class="subcolumns clearfix">
            <div class="c33l product-group-page-image">
                <div class="subcl">
                    <a href="$Link">$image.SetRatioSize(150,150)</a>
                </div>
            </div>
            <div class="c66r">
                <div class="subcr">
                    <p>$ShortDescription</p>
                    <div class="product-group-page-details">
                        <p><strong class="price">$Price.Nice</strong><br/>
                            <% sprintf(_t('Page.TAX', 'incl. %s%% VAT'),$tax.Rate) %><br />
                            <% _t('Page.PLUS_SHIPPING','plus shipping') %><br/>
                            <a href="$Link" title="$Title"><% _t('Page.DETAILS','details') %></a>
                        </p>
                    </div>
                    $productAddCartForm
                </div>
            </div>
        </div>
    </div>
</div>
<% end_control %>
