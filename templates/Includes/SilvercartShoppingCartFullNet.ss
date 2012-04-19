<table class="silvercart-shopping-cart-full">
    <% include SilvercartShoppingCartFullTableHead %>

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
                        <td colspan="3">$Name</td>
                        <td class="right">$PriceNetFormatted</td>
                        <td class="right">$Tax.Title</td>
                        <td class="right">$Quantity</td>
                        <td class="right">$PriceNetTotalFormatted</td>
                        <% if Top.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                <% end_control %>
            <% end_if %>
        <% end_control %>

        <% if HasChargesAndDiscountsForProducts %>
            <% control ChargesAndDiscountsForProducts %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if Top.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
            <% end_control %>
        <% end_if %>

        <tr>
            <td colspan="3">&nbsp;</td>
            <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
            <td class="right" id="Sum"><strong>$TaxableAmountNetWithoutFees.Nice</strong></td>

            <% if Top.EditableShoppingCart %>
                <td>&nbsp;</td>
            <% end_if %>
        </tr>

        <% if ShowFees %>
            <tr>
                <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %>:</td>
                <td colspan="4" class="right"><strong>$CarrierAndShippingMethodTitle <% control ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_control %></strong></td>
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
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="right" id="Sum"><strong>$TaxableAmountNetWithFees.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>
        
        <% control registeredModules %>
            <% if NonTaxableShoppingCartPositions %>
                <% control NonTaxableShoppingCartPositions %>
                    <tr>
                        <td colspan="3">$Name</td>
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

        <% if HasChargesAndDiscountsForTotal %>
            <% control ChargesAndDiscountsForTotal %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if Top.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
            <% end_control %>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                <td class="right"><strong>$AmountTotalNetWithoutVat.Nice</strong></td>

                <% if Top.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>
        
        <% if TaxTotal %>
            <% control TaxTotal %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="right"><% _t('SilvercartPage.ADDITIONAL_VAT','Additional VAT') %> ({$Rate}%)</td>
                    <td class="right">$Amount.Nice<% control ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_control %></td>

                    <% if Top.EditableShoppingCart %>
                        <td>&nbsp;</td>
                    <% end_if %>
                </tr>
            <% end_control %>
        <% end_if %>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td colspan="3" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
            <td class="right"><strong>$AmountTotalNet.Nice</strong></td>

            <% if Top.EditableShoppingCart %>
                <td>&nbsp;</td>
            <% end_if %>
        </tr>
    </tbody>
</table>
<% control ShippingMethod.ShippingFee %><% if PostPricing %><b>* <% _t('SilvercartPage.PLUS_SHIPPING') %>, <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></b><% end_if %><% end_control %>