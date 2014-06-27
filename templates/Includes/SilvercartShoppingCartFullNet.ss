<table class="silvercart-shopping-cart-full">
    <% include SilvercartShoppingCartFullTableHead %>

    <tbody>
        <% loop SilvercartShoppingCartPositions %>
            <% include SilvercartShoppingCartFullPosition %>
            
            <% if hasNotice %>
            <tr>
                <td colspan="<% if CurrentPage.EditableShoppingCart %>8<% else %>7<% end_if %>">
                    <p class="silvercart-message highlighted info16">
                        $getShoppingCartPositionNotices
                    </p>
                </td>
            </tr>
            <% end_if %>
        <% end_loop %>

        <% loop registeredModules %>
            <% if TaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                    <td class="right price" id="Sum"><strong><% with CurrentMember.getCart %>$TaxableAmountNetWithoutFeesAndChargesAndModules.Nice<% end_with %></strong></td>
                    <% if Top.EditableShoppingCart %>
                        <td>&nbsp;</td>
                    <% end_if %>
                </tr>
                <% loop TaxableShoppingCartPositions %>
                    <tr>
                        <td colspan="3">$Name</td>
                        <td class="right">$PriceNetFormatted</td>
                        <td class="right">$Tax.Title</td>
                        <td class="right">$getTypeSafeQuantity</td>
                        <td class="right price">$PriceNetTotalFormatted</td>
                        <% if CurrentPage.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                <% end_loop %>
            <% end_if %>
        <% end_loop %>

        <% if HasChargesAndDiscountsForProducts %>
            <% loop ChargesAndDiscountsForProducts %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if CurrentPage.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right price">$PriceFormatted</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <tr>
            <td colspan="3">&nbsp;</td>
            <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
            <td class="right price" id="Sum"><strong>$TaxableAmountNetWithoutFees.Nice</strong></td>

            <% if CurrentPage.EditableShoppingCart %>
                <td>&nbsp;</td>
            <% end_if %>
        </tr>

        <% if ShowFees %>
            <tr>
                <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %>:</td>
                <td colspan="4" class="right"><strong>$CarrierAndShippingMethodTitle <% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
                <td class="right price">$HandlingCostShipment.Nice</td>

                <% if CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
            <tr>
                <td colspan="2"><% _t('SilvercartPaymentMethod.SINGULARNAME') %>:</td>
                <td colspan="4" class="right"><strong>$payment.Name</strong></td>
                <td class="right price">$HandlingCostPayment.Nice</td>

                <% if CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="right price" id="Sum"><strong>$TaxableAmountNetWithFees.Nice</strong></td>

                <% if CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>
        
        <% loop registeredModules %>
            <% if NonTaxableShoppingCartPositions %>
                <% loop NonTaxableShoppingCartPositions %>
                    <tr>
                        <td colspan="3">$Name</td>
                        <td class="right">$PriceFormatted</td>
                        <td>&nbsp;</td>
                        <td class="right">$getTypeSafeQuantity</td>
                        <td class="right price">$PriceTotalFormatted</td>

                        <% if CurrentPage.EditableShoppingCart %>
                            <td>$removeFromCartForm</td>
                        <% end_if %>
                    </tr>
                <% end_loop %>
            <% end_if %>
        <% end_loop %>

        <% if HasChargesAndDiscountsForTotal %>
            <% with ChargesAndDiscountsForTotal %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if CurrentPage.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right price">$PriceFormatted</td>
                </tr>
            <% end_with %>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="3" class="right"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                <td class="right price"><strong>$AmountTotalNetWithoutVat.Nice</strong></td>

                <% if CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
            </tr>
        <% end_if %>
        
        <% if TaxTotal %>
            <% loop TaxTotal %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="right"><% _t('SilvercartPage.ADDITIONAL_VAT','Additional VAT') %> ({$Rate}%)</td>
                    <td class="right price">$Amount.Nice<% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></td>

                    <% if CurrentPage.EditableShoppingCart %>
                        <td>&nbsp;</td>
                    <% end_if %>
                </tr>
            <% end_loop %>
        <% end_if %>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td colspan="3" class="right"><strong><% _t('SilvercartPage.TOTAL','total') %></strong></td>
            <td class="right price"><strong>$AmountTotalNet.Nice</strong></td>

            <% if CurrentPage.EditableShoppingCart %>
                <td>&nbsp;</td>
            <% end_if %>
        </tr>

        <% loop registeredModules %>
            <% if IncludedInTotalShoppingCartPositions %>
                <% loop IncludedInTotalShoppingCartPositions %>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                        <td colspan="3" class="right">$Name</td>
                        <td class="right price">{$PriceTotalFormatted}</td>
                        <td>&nbsp;</td>
                    </tr>
                <% end_loop %>
            <% end_if %>
        <% end_loop %>
        
        <% if CurrentPage.EditableShoppingCart %>
            <% if addToEditableShoppingCartTable %>
                <% with addToEditableShoppingCartTable %>
                    <tr>
                        <td colspan="3">{$TitleField}&nbsp;</td>
                        <td colspan="3" class="right">{$RightTitleField}&nbsp;</td>
                        <td class="right price">{$PriceField.Nice}</td>
                        <td>&nbsp;</td>
                    </tr>
                <% end_with %>
            <% end_if %>
        <% end_if %>
    </tbody>
</table>
<% with ShippingMethod.ShippingFee %><% if PostPricing %><b>* <% _t('SilvercartPage.PLUS_SHIPPING') %>, <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></b><% end_if %><% end_with %>