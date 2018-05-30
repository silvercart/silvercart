<tr>
    <td class="img">
<% with $Product %>
    <% if $ListImage %>
        <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Title %>">{$ListImage.Pad(92,92)}</a>
    <% end_if %>
<% end_with %>
    </td>
    <td class="desc span4">
        <h5><a class="highlight" href="{$Product.Link}">{$getTitle}</a></h5>
        <ul class="unstyled">
            <li><a class="highlight" href="{$Product.Link}"><%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %>: {$getProductNumberShop}</a></li>
            <% if $getCartDescription %><li><small>{$getCartDescription}</small></li><% end_if %>
            <% if $addToTitle %><li><small>{$addToTitle}</small></li><% end_if %>
            <% if $Top.SiteConfig.enableStockManagement && $Product.StockQuantity < $Quantity %><li><small class="label label-info"><span class="icon icon-info-sign fa fa-info-sign"></span> <%t SilverCart\Model\Order\ShoppingCart.OnlyXLeft 'Only {quantity} left in stock. Delivery time may increase.' quantity=$Product.StockQuantity %></small></li><% end_if %>
        </ul>
    </td>
    <td class="sub-price text-right">
        {$getPrice(true).Nice}<span class="mobile-show-sm inline">
                <br/><%t SilverCart\Model\Product\Product.PRICE_SINGLE 'Price single' %></span>
        <p class="mobile-hide-sm">
            <small>{$Product.TaxRate}% <%t SilverCart\Model\Product\Product.VAT 'VAT' %></small>
        </p>
    </td>
<% if $CurrentPage.EditableShoppingCart %>
    <td class="text-right borderlr quantity">
        <div class="btn-group">
            <div class="pull-left input-prepend input-append form-prepend">{$DecrementPositionQuantityForm}</div>
            <div class="pull-left">
                <form action="/sc-action/addToCart" method="post">
                    <input type="hidden" name="productID" value="{$ProductID}">
                    <div class="input-prepend input-append">
                        <input type="text" class="text input-mini" name="productQuantity" value="{$TypeSafeQuantity}" id="productQuantity-{$ID}">
                    </div>
                </form>
            </div>
            <% if $isQuantityIncrementableBy %>
            <div class="pull-left input-append">{$IncrementPositionQuantityForm}</div>
            <% end_if %> 
        </div>
    </td>
<% else %>
    <td class="text-right borderlr quantity">
        <span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span>
    </td>
<% end_if %>
    <td class="total-price text-right">
        {$Price.Nice}<br/>
        <p class="mobile-hide-sm">
            <small> {$Product.TaxRate}% <%t SilverCart\Model\Product\Product.VAT 'VAT' %></small>
        </p>
    </td>
<% if $CurrentPage.EditableShoppingCart %>
    <td class="remove">{$RemovePositionForm}</td>
<% end_if %>
</tr>



