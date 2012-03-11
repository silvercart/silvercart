<table cellspacing="0" cellpadding="0" border="0">
<colgroup>
        <col width="20%"></col>
        <col width="45%"></col>
        <col width="15%"></col>
        <col width="5%"></col>
        <col width="15%"></col>
    </colgroup>
    <thead>
        <tr>
            <th><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %></th>
            <th><% _t('SilvercartProduct.COLUMN_TITLE') %></th>
            <th class="right"><% _t('SilvercartProduct.PRICE_SINGLE') %></th>
            <th class="right"><% _t('SilvercartProduct.VAT') %></th>
            <th class="right"><% _t('SilvercartProduct.QUANTITY') %></th>
            <th class="right"><% _t('SilvercartPrice.SINGULARNAME') %></th>
        </tr>
    </thead>
    <tbody>
        <% control SilvercartOrderListPositions %>
            <tr class="$EvenOrOdd">
                <td>$ProductNumber</td>
                <td>$Title.RAW</td>
                <td class="right">$Price.Nice</td>
                <td class="right">{$TaxRate}%</td>
                <td class="right">$Quantity</td>
                <td class="right">$PriceTotal.Nice</td>
            </tr>
            <% if productVariantDefinition %>
                <tr class="subrow">
                    <td colspan="6">
                        $productVariantDefinition.RAW
                    </td>
                </tr>
            <% end_if %>
            <% if SilvercartVoucherCode %>
                <tr class="subrow">
                    <td colspan="6">

                        <% if MoreThanOneProduct %>
                            <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERTITLE') %>
                            <ul>
                                <% control VoucherCodes %>
                                    <li>"<strong>$code</strong>"</li>
                                <% end_control %>
                            </ul>
                            <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                            <strong><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_PLURAL') %></strong>
                        <% else %>
                            <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERTITLE') %>
                            "<strong>$SilvercartVoucherCode</strong>".<br />
                            <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                            <strong><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_SINGULAR') %></strong>
                        <% end_if %>
                    </td>
                </tr>
            <% end_if %>
        <% end_control %>

        <!-- sub total without fees and charges -->
        <% control getTaxableAmountGrossWithoutFees(false,false) %>
        <tr class="new-block">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
                <td class="right"><strong><% _t('SilvercartPage.VALUE_OF_GOODS') %></strong></td>
                <td class="right"><strong>$Amount.Nice</strong></td>
        </tr>
        <% end_control %>

        <% if HasChargePositionsForProduct %>
            <!-- charges and discounts for product value -->
            <% control SilvercartOrderChargePositionsProduct %>
                <tr class="$EvenOrOdd">
                    <td colspan="2">$Title.RAW</td>
                    <td class="right">$Price.Nice</td>
                    <td class="right">{$TaxRate}%</td>
                    <td class="right">$Quantity</td>
                    <td class="right">$PriceTotal.Nice</td>
                </tr>
            <% end_control %>

            <!-- sub total without fees with product charges -->
            <% control getTaxableAmountGrossWithoutFees(true,false) %>
                <tr class="new-block">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                    <td class="right"><strong>$Amount.Nice</strong></td>
                </tr>
            <% end_control %>

            <!-- tax rates for sub total without fees -->
            <% control getTaxRatesWithoutFees(true,false) %>
                <tr class="new-block">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                    <td class="right">$Amount.Nice</td>
                </tr>
            <% end_control %>
        <% end_if %>

        <!-- fees -->
        <tr>
            <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %></td>
            <td colspan="3"><strong>$SilvercartShippingMethod.TitleWithCarrier</strong></td>
            <td class="right">$HandlingCostShipment.Nice</td>
        </tr>
        <tr>
            <td colspan="2"><% _t('SilvercartOrder.PAYMENTMETHODTITLE') %></td>
            <td colspan="3"><strong>$SilvercartPaymentMethod.Name</strong></td>
            <td class="right">$HandlingCostPayment.Nice</td>
        </tr>

        <% if SilvercartOrderChargePositionsTotal %>
            <!-- sub total -->
            <% control getTaxableAmountGrossWithFees(true,false) %>
                <tr class="new-block">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                    <td class="right"><strong>$Amount.Nice</strong></td>
                </tr>
            <% end_control %>

            <!-- tax rates for sub total -->
            <% control getTaxRatesWithFees(true,false) %>
                <tr class="new-block">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="<% if Top.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.INCLUDED_VAT','included VAT') %> ({$Rate}%)</td>
                    <td class="right">$Amount.Nice</td>
                </tr>
            <% end_control %>

            <!-- non-taxable positions -->
            <% control SilvercartOrderPositionsWithoutTax %>
                <tr>
                    <td>&nbsp;</td>
                    <td>$Title.RAW</td>
                    <td class="right">$PriceNice</td>
                    <td class="right"></td>
                    <td class="right">$Quantity</td>
                    <td class="right">$PriceTotalNice</td>
                </tr>
                <% if SilvercartVoucherCode %>
                    <tr class="subrow">
                        <td colspan="6">

                            <% if MoreThanOneProduct %>
                                <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERTITLE') %>
                                <ul>
                                    <% control VoucherCodes %>
                                        <li>"<strong>$code</strong>"</li>
                                    <% end_control %>
                                </ul>
                                <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                                <strong><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_PLURAL') %></strong>
                            <% else %>
                                <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERTITLE') %>
                                "<strong>$SilvercartVoucherCode</strong>".<br />
                                <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                                <strong><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_SINGULAR') %></strong>
                            <% end_if %>
                        </td>
                    </tr>
                <% end_if %>
            <% end_control %>

            <!-- charges and discounts for the shopping cart value -->
            <% control SilvercartOrderChargePositionsTotal %>
                <tr class="$EvenOrOdd">
                    <td colspan="2">$Title.RAW</td>
                    <td class="right">$Price.Nice</td>
                    <td class="right">{$TaxRate}%</td>
                    <td class="right">$Quantity</td>
                    <td class="right">$PriceTotal.Nice</td>
                </tr>
            <% end_control %>
        <% end_if %>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="right"><strong><% _t('SilvercartPage.TOTAL') %></strong></td>
            <td class="right"><strong>$AmountTotal.Nice</strong></td>
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
    </tbody>
</table>
