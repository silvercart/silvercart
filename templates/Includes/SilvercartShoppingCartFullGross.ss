<table class="silvercart-shopping-cart-full">
    <% include SilvercartShoppingCartFullTableHead %>

    <tbody>
        <% loop SilvercartShoppingCartPositions %>
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
        <% end_loop %>

        <% loop registeredModules %>
            <% if TaxableShoppingCartPositions %>
                <% loop TaxableShoppingCartPositions %>
                    <tr>
                        <td colspan="3">$Name</td>
                        <td class="right">$PriceFormatted</td>
                        <td class="right">$Tax.Title</td>
                        <td class="right">$getTypeSafeQuantity</td>
                        <td class="right">$PriceTotalFormatted</td>
                        <% if Top.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                <% end_loop %>
            <% end_if %>
        <% end_loop %>

        <% if HasFeesOrChargesOrModules %>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.VALUE_OF_GOODS','Value of goods') %></strong></td>
                <td class="right" id="Sum"><strong>$TaxableAmountGrossWithoutFeesAndCharges.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>
        
        <% if HasChargesAndDiscountsForProducts %>
            <% loop ChargesAndDiscountsForProducts %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if Top.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
            <% end_loop %>
            
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                <td class="right" id="Sum"><strong>$TaxableAmountGrossWithoutFees.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>

        <% if HasFeesOrChargesOrModules %>
            <% if TaxRatesWithoutFees %>
                <% loop TaxRatesWithoutFees %>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td colspan="3" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                        <td class="right">$Amount.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                <% end_loop %>
            <% end_if %>
        <% end_if %>

        <% if ShowFees %>
            <tr>
                <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %>:</td>
                <td colspan="4" class="right"><strong>$CarrierAndShippingMethodTitle <% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
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
        <% end_if %>
        
        <% if HasChargesAndDiscountsForTotal %>
            <% if ShowFees %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                    <td class="right" id="Sum"><strong>$TaxableAmountGrossWithFees.Nice</strong></td>

                    <% if Top.EditableShoppingCart %>
                        <td>&nbsp;</td>
                    <% end_if %>
                </tr>
                <% if TaxRatesWithFees %>
                    <% loop TaxRatesWithFees %>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td colspan="3" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                            <td class="right">$Amount.Nice</td>

                            <% if Top.EditableShoppingCart %>
                                <td>&nbsp;</td>
                            <% end_if %>
                        </tr>
                    <% end_loop %>
                <% end_if %>
            <% end_if %>
            <% with ChargesAndDiscountsForTotal %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if Top.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
            <% end_with %>

            <% loop registeredModules %>
                <% if NonTaxableShoppingCartPositions %>
                    <% loop NonTaxableShoppingCartPositions %>
                        <tr>
                            <td colspan="3">$Name</td>
                            <td class="right">$PriceFormatted</td>
                            <td>&nbsp;</td>
                            <td class="right">$getTypeSafeQuantity</td>
                            <td class="right">$PriceTotalFormatted</td>

                            <% if Top.EditableShoppingCart %>
                                <td>$removeFromCartForm</td>
                            <% end_if %>
                        </tr>
                    <% end_loop %>
                <% end_if %>
            <% end_loop %>

            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
                <td class="right"><strong>$AmountTotal.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>

            <% if TaxTotal %>
                <% with TaxTotal %>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td colspan="3" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                        <td class="right">$Amount.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                <% end_with %>
            <% end_if %>
        <% else %>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
                <td class="right"><strong>$AmountTotal.Nice<% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>

            <% if TaxTotal %>
                <% with TaxTotal %>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td colspan="3" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                        <td class="right">$Amount.Nice</td>

                        <% if Top.EditableShoppingCart %>
                            <td>&nbsp;</td>
                        <% end_if %>
                    </tr>
                <% end_with %>
            <% end_if %>
        <% end_if %>
        
        <% if Top.EditableShoppingCart %>
            <% if addToEditableShoppingCartTable %>
                <% with addToEditableShoppingCartTable %>
                    <tr>
                        <td colspan="3">{$TitleField}&nbsp;</td>
                        <td colspan="3" class="right">{$RightTitleField}&nbsp;</td>
                        <td class="right">{$PriceField.Nice}</td>
                        <td>&nbsp;</td>
                    </tr>
                <% end_with %>
            <% end_if %>
        <% end_if %>
        
    </tbody>
</table>
<% with ShippingMethod.ShippingFee %><% if PostPricing %><b>* <% _t('SilvercartPage.PLUS_SHIPPING') %>, <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></b><% end_if %><% end_with %>