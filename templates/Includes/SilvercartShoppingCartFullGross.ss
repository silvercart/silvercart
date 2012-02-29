<table class="silvercart-shopping-cart-full">
    <% include SilvercartShoppingCartFullTableHead %>

    <tbody>
        <% control SilvercartShoppingCartPositions %>
            <% include SilvercartShoppingCartFullPosition %>

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
                        <td colspan="2">
                            $Name
                        </td>
                        <td class="right">$PriceFormatted</td>
                        <td class="right">$Tax.Title</td>

                        <% if Top.EditableShoppingCart %>
                            <td class="right borderlr" colspan="3">
                                <div class="subcolumns">
                                    <div class="c33l">&nbsp;</div>
                                    <div class="c33l">
                                        <div class="silvercart-quantity-value right">
                                            $Quantity
                                        </div>
                                    </div>
                                    <div class="c33r">&nbsp;</div>
                                </div>
                            </td>
                        <% else %>
                            <td class="right borderlr">
                                <div class="silvercart-quantity-value right">
                                    $Quantity
                                </div>
                            </td>
                        <% end_if %>

                        <td class="right">$PriceTotalFormatted</td>

                        <% if Top.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                <% end_control %>
            <% end_if %>
        <% end_control %>

        <% if HasFeesOrChargesOrModules %>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><strong><% _t('SilvercartPage.VALUE_OF_GOODS','Value of goods') %></strong></td>
                <td class="right" id="Sum"><strong>$TaxableAmountGrossWithoutFeesAndCharges.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>
        
        <% if HasChargesAndDiscountsForProducts %>
            <% control ChargesAndDiscountsForProducts %>
                <tr>
                    <td colspan="3">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if Top.EditableShoppingCart %>6<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
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
        <% end_if %>

        <% if HasFeesOrChargesOrModules %>
            <% if TaxRatesWithoutFees %>
                <% control TaxRatesWithoutFees %>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <% if Top.showPricesGross %>
                            <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                        <% else %>
                            <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.ADDITIONAL_VAT','Additional VAT') %> ({$Rate}%)</td>
                        <% end_if %>
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
        <% end_if %>
        
        <% if HasChargesAndDiscountsForTotal %>
            <% if ShowFees %>
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
                            <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                            <td class="right">$Amount.Nice</td>

                            <% if Top.EditableShoppingCart %>
                                <td>&nbsp;</td>
                            <% end_if %>
                        </tr>
                    <% end_control %>
                <% end_if %>
            <% end_if %>
            <% control ChargesAndDiscountsForTotal %>
                <tr>
                    <td colspan="3">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if Top.EditableShoppingCart %>6<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
            <% end_control %>

            <% control registeredModules %>
                <% if NonTaxableShoppingCartPositions %>
                    <% control NonTaxableShoppingCartPositions %>
                        <tr>
                            <td colspan="2">$Name</td>
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

            <% if TaxTotal %>
                <% control TaxTotal %>
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
        <% else %>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
                <td class="right"><strong>$AmountTotal.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>

            <% if TaxTotal %>
                <% control TaxTotal %>
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
    </tbody>
</table>

