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
                    <col width="12%"></col>
                    <col width=""></col>
                    <col width="10%"></col>
                    <col width="7%"></col>
                    <col width="15%"></col>
                    <col width="12%"></col>
                    <% if Top.EditableShoppingCart %>
                        <col width="5%"></col>
                    <% end_if %>
                </colgroup>
                <thead>
                    <tr>
                        <th class="left"><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %></th>
                        <th class="left"><% _t('SilvercartProduct.IMAGE','product image') %></th>
                        <th class="left"><% _t('SilvercartPage.PRODUCTNAME','product name') %></th>
                        <th class="right"><% if CurrentMember.showPricesGross %><% _t('SilvercartProduct.PRICE_SINGLE', 'price single') %><% else %><% _t('SilvercartProduct.PRICE_SINGLE_NET', 'price single net') %><% end_if %></th>
                        <th class="right"><% _t('SilvercartProduct.VAT','VAT') %></th>
                        <th class="right"><% _t('SilvercartProductPage.QUANTITY') %></th>
                        <th class="right"><% if CurrentMember.showPricesGross %><% _t('SilvercartProduct.PRICE', 'price') %><% else %><% _t('SilvercartProduct.PRICE_NET', 'price net') %><% end_if %></th>
                        <% if Top.EditableShoppingCart %>
                            <th>&nbsp;</th>
                        <% end_if %>
                    </tr>
                </thead>

                <tbody>
                <% control SilvercartShoppingCartPositions %>
                    <% include SilvercartShoppingCartFullPosition %>
                    
                    <% if hasNotice %>
                    <tr>
                        <td colspan="<% if Top.EditableShoppingCart %>8<% else %>7<% end_if %>">
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
                        <td>&nbsp;</td>
                        <td>$Name</td>
                        <td class="right">$PriceFormatted</td>
                        <td class="right">$SilvercartTax.Title</td>
                        <td class="right">$Quantity</td>
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
                        <td colspan="4" class="right"><strong><% if Top.showPricesGross %><% _t('SilvercartPage.SUBTOTAL','subtotal') %><% else %><% _t('SilvercartPage.SUBTOTAL_NET','subtotal (net)') %><% end_if %></strong></td>
                        <td class="right"><strong>$TaxableAmountGrossWithoutFees.Nice</strong></td>
                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>

                <% if Top.EditableShoppingCart %>
                    <% if TaxRatesWithoutFees %>
                        <% control TaxRatesWithoutFees %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="4" class="right">
                            <% if Top.showPricesGross %>
                                <% _t('SilvercartPage.INCLUDED_VAT','included VAT') %>
                            <% else %>
                                <% _t('SilvercartPage.ADDITIONAL_VAT','additional VAT') %>
                            <% end_if %> ({$Rate}%)
                        </td>
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
                        <td colspan="4" class="right"><strong>$CarrierAndShippingMethodTitle</strong></td>
                        <td class="right">$HandlingCostShipment.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                    <tr>
                        <td colspan="2"><% _t('SilvercartPaymentMethod.SINGULARNAME') %>:</td>
                        <td colspan="4" class="right"><strong>$payment.Name</strong></td>
                        <td class="right">$HandlingCostPayment.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="4" class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                        <td class="right"><strong>$TaxableAmountGrossWithFees.Nice</strong></td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>

                    <% if TaxRatesWithFees %>
                        <% control TaxRatesWithFees %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="4" class="right">
                            <% if Top.showPricesGross %>
                                <% _t('SilvercartPage.INCLUDED_VAT','included VAT') %>
                            <% else %>
                                <% _t('SilvercartPage.ADDITIONAL_VAT','additional VAT') %>
                            <% end_if %> ({$Rate}%)
                        </td>
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
                        <td>&nbsp;</td>
                        <td>$Name</td>
                        <td class="right">$PriceFormatted</td>
                        <td>&nbsp;</td>
                        <td class="right">$Quantity</td>
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
                        <td colspan="4" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
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
