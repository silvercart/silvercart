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
            <th class="left"><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %></th>
            <th class="left"><% _t('SilvercartProduct.COLUMN_TITLE') %></th>
            <th class="right"><% _t('SilvercartProduct.PRICE_SINGLE') %></th>
            <th class="right"><% _t('SilvercartProduct.VAT') %></th>
            <th class="right"><% _t('SilvercartProduct.QUANTITY') %></th>
            <th class="right"><% _t('SilvercartPrice.SINGULARNAME') %></th>
        </tr>
    </thead>
    <tbody>
        <% loop SilvercartOrderListPositions %>
            <tr class="$EvenOrOdd">
                <td class="left">$ProductNumber</td>
                <td class="left">$Title.RAW<% if ShortDescription %><br/>$ShortDescription.RAW<% end_if %><% if addToTitle %><br/>$addToTitle<% end_if %></td>
                <td class="right">$Price.Nice</td>
                <td class="right">{$TaxRate}%</td>
                <td class="right quantity">$getTypeSafeQuantity</td>
                <td class="right">$PriceTotal.Nice</td>
            </tr>
            <% if SilvercartVoucherCode %>
                <tr class="subrow">
                    <td colspan="6">

                        <% if MoreThanOneProduct %>
                            <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERTITLE') %>
                            <ul>
                                <% loop VoucherCodes %>
                                    <li>"<strong>$code</strong>"</li>
                                <% end_loop %>
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
        <% end_loop %>

        <% if HasChargePositionsForProduct %>
            <!-- charges and discounts for product value -->
            <% loop SilvercartOrderChargePositionsProduct %>
                <tr class="$EvenOrOdd">
                    <td colspan="2">$Title.RAW</td>
                    <td class="right">$Price.Nice</td>
                    <td class="right">{$TaxRate}%</td>
                    <td class="right">$getTypeSafeQuantity</td>
                    <td class="right">$PriceTotal.Nice</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <!-- sub total without fees with product charges -->
        <% with getTaxableAmountNetWithoutFees(true,false) %>
            <tr class="new-block">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="right">$Amount.Nice</td>
            </tr>
        <% end_with %>

        <!-- fees -->
        <tr>
            <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %></td>
            <td colspan="3"><strong>$SilvercartShippingMethod.TitleWithCarrier <% with SilvercartShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
            <td class="right">$HandlingCostShipment.Nice</td>
        </tr>
        <tr>
            <td colspan="2"><% _t('SilvercartOrder.PAYMENTMETHODTITLE') %></td>
            <td colspan="3"><strong>$SilvercartPaymentMethod.Name</strong></td>
            <td class="right">$HandlingCostPayment.Nice</td>
        </tr>

        <!-- sub total -->
        <% with getTaxableAmountNetWithFees(true,false) %>
            <tr class="new-block">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="right">$Amount.Nice</td>
            </tr>
        <% end_with %>

        <!-- charges and discounts for the shopping cart value -->
        <% loop SilvercartOrderChargePositionsTotal %>
            <tr class="$EvenOrOdd">
                <td colspan="2">$Title.RAW</td>
                <td class="right">$Price.Nice</td>
                <td class="right">{$TaxRate}%</td>
                <td class="right">$getTypeSafeQuantity</td>
                <td class="right">$PriceTotal.Nice</td>
            </tr>
        <% end_loop %>

        <% if TaxTotal %>
            <% loop TaxTotal %>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="<% if CurrentPage.EditableShoppingCart %>5<% else %>3<% end_if %>" class="right"><% _t('SilvercartPage.ADDITIONAL_VAT','Additional VAT') %> ({$Rate}%)</td>
                    <td class="right">$Amount.Nice</td>

                    <% if CurrentPage.EditableShoppingCart %>
                        <td>&nbsp;</td>
                    <% end_if %>
                </tr>
            <% end_loop %>
        <% end_if %>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="right"><strong><% _t('SilvercartPage.TOTAL') %></strong></td>
            <td class="right"><strong>$AmountTotal.Nice<% with SilvercartShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
        </tr>

        <% if HasIncludedInTotalPositions %>
            <% loop SilvercartOrderIncludedInTotalPositions %>
                <tr class="$EvenOrOdd">
                    <td colspan="5" class="right">$Title.RAW</td>
                    <td class="right">$Price.Nice</td>
                </tr>
            <% end_loop %>
        <% end_if %>
    </tbody>
</table>
<% with SilvercartShippingMethod.ShippingFee %><% if PostPricing %><b>* <% _t('SilvercartPage.PLUS_SHIPPING') %>, <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></b><% end_if %><% end_with %>
