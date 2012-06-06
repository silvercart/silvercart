<% if Elements %>
    <% control Elements %>
<div class="product-group-holder-entry clearfix $EvenOdd">
    <div class="product-group-holder-entry_content">
        <h3><a href="$Link">$Title.HTML</a></h3>
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
                    $Content.Raw
                </div>
            </div>
        </div>
        
        <div class="silvercart-product-group-holder-list-productlist">
            <div class="silvercart-product-group-holder-list-productlist_content clearfix">
                <% control getProductsForced(6) %>
                    <a href="$Link" class="silvercart-product-group-holder-list-productlist-entry" title="$Title">
                        <span class="silvercart-product-group-holder-list-productlist-entry_content">
                            <% control getSilvercartImages.First %>
                                $Image.SetRatioSize(90,90)
                            <% end_control %>
                        </span>
                    </a>
                <% end_control %>
            </div>
        </div>
        
        <div class="product-group-holder-entry-foot">
            <% if hasProductCount(0) %>
            <% else %>
            <% if hasProductCount(1) %>
            <a href="$Link" title="<% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_SINGULAR','details'),$ActiveSilvercartProducts.Count) %>"><% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_SINGULAR','details'),$ActiveSilvercartProducts.Count) %> &gt;&gt;</a>
            <% else %>
            <a href="$Link" title="<% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_PLURAL','details'),$ActiveSilvercartProducts.Count) %>"><% sprintf(_t('SilvercartProductGroupHolder.SHOW_PRODUCTS_WITH_COUNT_PLURAL','details'),$ActiveSilvercartProducts.Count) %> &gt;&gt;</a>
            <% end_if %>
            <% end_if %>
        </div>
    </div>
</div>
    <% end_control %>
<% end_if %>
