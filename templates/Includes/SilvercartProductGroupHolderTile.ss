<% if Elements %>
    <% control Elements %>
        <% if MultipleOf(2) %>
            <div class="c50r product-group-holder-entry tile $EvenOdd">
        <% else %>
        <div class="subcolumns equalize clearfix">
            <div class="c50l product-group-holder-entry tile $EvenOdd">
        <% end_if %>
                <div class="product-group-holder-entry_content">
                    <div class="subcolumns equalize">
                        <div class="c50l">
                            <div class="subcl">
                                <h3><a href="$Link" title="$Title">$MenuTitle.HTML</a></h3>
                            </div>
                        </div>
                        <div class="c50r product-group-holder-entry-link">
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
                    <div class="subcolumns equalize">
                        <div class="c66l">
                            <div class="subcl">
                                <p>$Content.LimitWordCount(12)</p>
                            </div>
                        </div>
                        <div class="c33r product-group-holder-entry-image">
                            <div class="subcr">
                                <% if groupPicture %>
                                    <a href="$Link">$groupPicture.SetRatioSize(90,90)</a>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <% if MultipleOf(2) %>
        </div>
        <% else_if Last %>
        </div>
        <% end_if %>
    <% end_control %>
<% end_if %>
