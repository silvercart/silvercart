<% if SilvercartErrors %>
    <div class="silvercart-error-list">
        <div class="silvercart-error-list_content">
            $SilvercartErrors
        </div>
    </div>
<% end_if %>

<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <% control CurrentMember %>
        <% control SilvercartShoppingCart %>
            <table class="silvercart-shopping-cart-full">
                <colgroup>
                    <col width="12%"></col>
                    <col width=""></col>
                    <col width="12%"></col>
                    <col width="7%"></col>
                    
                    <% if Top.EditableShoppingCart %>
                        <col width="5%"></col>
                        <col width="5%"></col>
                        <col width="5%"></col>
                    <% else %>
                        <col width="15%"></col>
                    <% end_if %>
                    
                    <col width="12%"></col>
                    
                    <% if Top.EditableShoppingCart %>
                        <col width="5%"></col>
                    <% end_if %>
                </colgroup>
                <thead>
                    <tr>
                        <th class="left"><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %></th>
                        <th class="left"><% _t('SilvercartPage.PRODUCTNAME','product name') %></th>
                        <th class="right"><% _t('SilvercartProduct.PRICE_SINGLE', 'price single') %></th>
                        <% if Top.showPricesGross %>
                            <th class="right"><% _t('SilvercartProduct.VAT','VAT') %></th>
                        <% else %>
                            <th>&nbsp;</th>
                        <% end_if %>
                        
                        <th<% if Top.EditableShoppingCart %> colspan="3"<% end_if %> class="right"><% _t('SilvercartProductPage.QUANTITY') %></th>
                        
                        <th class="right"><% _t('SilvercartProduct.PRICE') %></th>

                        <% if Top.EditableShoppingCart %>
                            <th>&nbsp;</th>
                        <% end_if %>
                    </tr>
                </thead>

                <tbody>
                <% control SilvercartShoppingCartPositions %>
                    <tr<% if Last %> class="separator"<% end_if %>>
                        <td><a href="$silvercartProduct.Link">$SilvercartProduct.ProductNumberShop</a></td>
                        <td><a href="$silvercartProduct.Link">$SilvercartProduct.Title</a></td>
                        <td class="right">$SilvercartProduct.Price.Nice</td>
                        <% if Top.showPricesGross %>
                            <td class="right">{$SilvercartProduct.TaxRate}%</td>
                        <% else %>
                            <td>&nbsp;</td>
                        <% end_if %>
                        
                        <% if Top.EditableShoppingCart %>
                            <td>$DecrementPositionQuantityForm</td>
                        <% end_if %>
                        
                        <td class="right">
                            $Quantity
                        </td>
                        <% if Top.EditableShoppingCart %>
                            <% if isQuantityIncrementableBy %>
                                <td>$IncrementPositionQuantityForm</td>
                            <% else %>
                                <td>&nbsp;</td>
                            <% end_if %>
                        <% end_if %>
                       <td class="right">$Price.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            
                            <td>$RemovePositionForm</td>
                        <% end_if %>
                    </tr>
                    
                    <% if hasNotice %>
                    <tr>
                        <td colspan="<% if Top.EditableShoppingCart %>9<% else %>6<% end_if %>">
                            <p class="silvercart-message highlighted info16">
                                $getShoppingCartPositionNotices
                            </p>
                        </td>
                    </tr>
                    <% end_if %>
                <% end_control %>

                <% control registeredModules %>
                    <% if TaxableShoppingCartPositions %>
                        <% control TaxableShoppingCartPositions %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>$Name</td>
                        <td class="right">$PriceFormatted</td>
                        <% if Top.showPricesGross %>
                            <td class="right">$SilvercartTax.Title</td>
                        <% else %>
                            <td>&nbsp;</td>
                        <% end_if %>
                        
                        <% if Top.EditableShoppingCart %>
                            <th>&nbsp;</th>
                        <% end_if %>
                        
                        <td class="right">$Quantity</td>
                        
                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                        
                        <td class="right">$PriceTotalFormatted</td>

                        <% if Top.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                        <% end_control %>
                    <% end_if %>
                <% end_control %>

                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                        <td class="right" id="Sum"><strong>$TaxableAmountGrossWithoutFees.Nice</strong></td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>

                <% if Top.showPricesGross %>
                    <% if TaxRatesWithoutFees %>
                        <% control TaxRatesWithoutFees %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                        <td class="right">$Amount.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                        <% end_control %>
                    <% end_if %>
                <% end_if %>

                <% if ShowFees %>
                    <tr>
                        <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %>:</td>
                        <td colspan="3" class="right"><strong>$CarrierAndShippingMethodTitle</strong></td>
                        <td class="right">$HandlingCostShipment.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                    <tr>
                        <td colspan="2"><% _t('SilvercartPaymentMethod.SINGULARNAME') %>:</td>
                        <td colspan="3" class="right"><strong>$payment.Name</strong></td>
                        <td class="right">$HandlingCostPayment.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                        <td class="right" id="Sum"><strong>$TaxableAmountGrossWithFees.Nice</strong></td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>

                    <% if TaxRatesWithFees %>
                        <% control TaxRatesWithFees %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="3" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                        <td class="right">$Amount.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                        <% end_control %>
                    <% end_if %>

                <% end_if %>

                <% control registeredModules %>
                    <% if NonTaxableShoppingCartPositions %>
                        <% control NonTaxableShoppingCartPositions %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>$Name</td>
                        <td class="right">$PriceFormatted</td>
                        <td>&nbsp;</td>
                        
                        <% if Top.EditableShoppingCart %>
                            <th>&nbsp;</th>
                        <% end_if %>
                        
                        <td class="right">$Quantity</td>
                        
                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                        
                        <td class="right">$PriceTotalFormatted</td>

                        <% if Top.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                        <% end_control %>
                    <% end_if %>
                <% end_control %>

                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
                        <td class="right"><strong>$AmountTotal.Nice</strong></td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                </tbody>

            </table>

            <% if Top.EditableShoppingCart %>
                <div class="shoppingCartActions">
                    <% if registeredModules %>
                        <% control registeredModules %>
                            <% if ShoppingCartActions %>
                                <% control ShoppingCartActions %>
                                    $moduleOutput
                                <% end_control %>
                            <% end_if %>
                        <% end_control %>
                    <% end_if %>
                </div>
            <% end_if %>
          
        <% end_control %>
    <% end_control %>
<% else %>
    <p><% _t('SilvercartCartPage.CART_EMPTY', 'Your cart is empty') %></p>
<% end_if %>
