<table cellspacing="0" cellpadding="0" border="0">
    <% control SilvercartOrderListPositions %>
        <tr class="$EvenOrOdd">
            <td>$ProductNumber</td>
            <td>$Title</td>
            <td class="right">$Price.Nice</td>
            <td class="right">{$TaxRate}%</td>
            <td class="right">$Quantity</td>
            <td class="right">$PriceTotal.Nice</td>
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

    <!-- sub total without fees and charges -->
    <% control getTaxableAmountGrossWithoutFees(false,false) %>
    <tr class="new-block">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
            <td><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
            <td class="right">$Amount.Nice</td>
    </tr>
    <% end_control %>


    <!-- tax rates for sub total without fees and charges -->
    <% control getTaxRatesWithoutFees(false,false) %>
        <tr class="new-block">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><% _t('SilvercartPage.INCLUDED_VAT') %> ({$Rate}%)</td>
        <td class="right">$Amount.Nice</td>
    </tr>
    <% end_control %>

    <% if HasChargePositionsForProduct %>
        <!-- charges and discounts for product value -->
        <% control SilvercartOrderChargePositionsProduct %>
            <tr class="$EvenOrOdd">
                <td colspan="2">$Title</td>
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
                <td><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="right">$Amount.Nice</td>
            </tr>
        <% end_control %>

        <!-- tax rates for sub total without fees -->
        <% control getTaxRatesWithoutFees(true,false) %>
            <tr class="new-block">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><% _t('SilvercartPage.INCLUDED_VAT') %> ({$Rate}%)</td>
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

    <!-- sub total -->
    <% control getTaxableAmountGrossWithFees(true,false) %>
        <tr class="new-block">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
            <td><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
            <td class="right">$Amount.Nice</td>
    </tr>
    <% end_control %>

    <!-- tax rates for sub total -->
    <% control getTaxRatesWithFees(true,false) %>
        <tr class="new-block">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><% _t('SilvercartPage.INCLUDED_VAT') %> ({$Rate}%)</td>
        <td class="right">$Amount.Nice</td>
    </tr>
    <% end_control %>

    <!-- non-taxable positions -->
    <% control SilvercartOrderPositionsWithoutTax %>
        <tr>
            <td>&nbsp;</td>
            <td>$Title</td>
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
            <td colspan="2">$Title</td>
            <td class="right">$Price.Nice</td>
            <td class="right">{$TaxRate}%</td>
            <td class="right">$Quantity</td>
            <td class="right">$PriceTotal.Nice</td>
        </tr>
    <% end_control %>

    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="right"><strong><% _t('SilvercartPage.TOTAL') %></strong></td>
        <td class="right"><strong>$AmountTotal.Nice</strong></td>
    </tr>
</table>
