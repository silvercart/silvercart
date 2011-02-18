<% control Elements %>
<div class="product-group-holder-entry clearfix $EvenOdd">
    <div class="product-group-holder-entry_content">
        <h3><a href="$Link">$Title</a></h3>
        <div class="subcolumns clearfix">
            <div class="c33l product-group-holder-entry-image">
                <div class="subcl">
                    <% if groupPicture %>
                    <a href="$Link">$groupPicture.SetRatioSize(210,210)</a>
                    <% end_if %>
                </div>
            </div>
            <div class="c66r">
                <div class="subcr">
                    $Content
                </div>
            </div>
        </div>
        <div class="product-group-holder-entry-foot">
            <% if hasProductCount(0) %>
            <% else %>
            <% if hasProductCount(1) %>
            <a href="$Link" title="<% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_SINGULAR','details'),$SilvercartProducts.Count) %>"><% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_SINGULAR','details'),$SilvercartProducts.Count) %> &gt;&gt;</a>
            <% else %>
            <a href="$Link" title="<% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_PLURAL','details'),$SilvercartProducts.Count) %>"><% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_PLURAL','details'),$SilvercartProducts.Count) %> &gt;&gt;</a>
            <% end_if %>
            <% end_if %>
        </div>
    </div>
</div>
<% end_control %>
