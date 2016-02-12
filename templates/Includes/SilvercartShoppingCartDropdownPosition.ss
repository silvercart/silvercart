<table class="table-cart silvercart-shopping-cart-full">
    <tbody>
    <% loop SilvercartShoppingCartPositions %>
        <tr>
            <td class="sc-product-cart">
                <% if SilvercartProduct.getSilvercartImages %>
                    <% loop SilvercartProduct.getSilvercartImages %>
                        <% if First %>
                            <a href="$silvercartProduct.Link" title="<% sprintf(_t('SilvercartPage.SHOW_DETAILS_FOR','details'),$Image.Title) %>">$Image.SetSize(62,62)</a>
                        <% end_if %>
                    <% end_loop %>
                <% end_if %>
            </td>
            <td class="sc-product-cart">
                
                <div class="sc-product-cart-description">
                    <p><a class="highlight" href="$silvercartProduct.Link"><strong>$getTitle</strong></a></h4>
                    <ul class="unstyled">
                        <li><small><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %>: $getProductNumberShop</small></li>
                        <% if getCartDescription %><li><small>$getCartDescription</small></li><% end_if %>
                    </ul>
                </div> 
            </td>   
            <td class="cart-product-setting">
                <p>
                    <strong>{$Price.Nice}</strong><br/>
                    <small>({$getTypeSafeQuantity}x $getPrice(true).Nice)</small>
                </p>
                <%-- @TODO register $RemovePositionForm for Dropdown Cart --%>
               <% loop registeredModules %>
            <% loop TaxableShoppingCartPositions %>
           $RemovePositionForm 
           <% end_loop %>
           <% loop NonTaxableShoppingCartPositions %>
           $RemovePositionForm
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
                <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>" class="btn btn-small">
                    <% _t('SilvercartPage.CART', 'cart') %>
                </a>
                <% end_with %>
                <% with $CurrentPage.PageByIdentifierCode(SilvercartCheckoutStep) %>
                <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>" class="btn btn-small btn-primary">
                    <% _t('SilvercartPage.CHECKOUT', 'checkout') %>
                </a>
                <% end_with %>
                <strong class="total pull-right"><% _t('Silvercart.TOTAL') %>: {$AmountTotal.Nice}</strong> 
            </td>
        </tr>
    </tfoot> 
</table>





















