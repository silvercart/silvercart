<table class="table-cart silvercart-shopping-cart-full">
    <tbody>
    <% loop $ShoppingCartPositions %>
        <tr>
            <td class="sc-product-cart">
                <% if $Product.ListImage %>
                    <a href="{$Product.Link}" title="<%t SilverCart\Model\Pages\Page.SHOW_DETAILS_FOR 'Show details for {title}' title=$Product.Title %>">{$Product.ListImage.Pad(62,62)}</a>
                <% end_if %>
            </td>
            <td class="sc-product-cart">
                
                <div class="sc-product-cart-description">
                    <p><a class="highlight" href="$silvercartProduct.Link"><strong>$getTitle</strong></a></h4>
                    <ul class="unstyled">
                        <li><small><%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %>: $getProductNumberShop</small></li>
                        <% if getCartDescription %><li><small>$getCartDescription</small></li><% end_if %>
                    </ul>
                </div> 
            </td>   
            <td class="cart-product-setting">
                <p>
                    <strong>{$Price.Nice}</strong><br/>
                    <small>({$getTypeSafeQuantity}x $getPrice(true).Nice)</small>
                </p>
                {$RemovePositionForm}
            <% loop registeredModules %>
                <% loop TaxableShoppingCartPositions %>
                    {$RemovePositionForm}
                <% end_loop %>
                <% loop NonTaxableShoppingCartPositions %>
                    {$RemovePositionForm}
                <% end_loop %>
            <% end_loop %>
            </td>
        </tr>
    <% end_loop %>
    </tbody>
    <tfoot>
        <tr>
            <td class="sc-product-cart" colspan="3">
                <% with $CurrentPage.PageByIdentifierCode(SilvercartCartPage) %>
                <a href="$Link" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title} page' title=$Title.XML %>" class="btn btn-small">
                    <%t SilverCart\Model\Pages\Page.CART 'cart' %>
                </a>
                <% end_with %>
                <% with $CurrentPage.PageByIdentifierCode(SilvercartCheckoutStep) %>
                <a href="$Link" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title} page' title=$Title.XML %>" class="btn btn-small btn-primary">
                    <%t SilverCart\Model\Pages\Page.CHECKOUT 'checkout' %>
                </a>
                <% end_with %>
                <strong class="total pull-right"><%t SilverCart\Model\Pages\Page.TOTAL 'Total' %>: {$AmountTotal.Nice}</strong> 
            </td>
        </tr>
    </tfoot> 
</table>





















