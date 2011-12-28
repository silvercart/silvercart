<% control CustomersOrder %>
    <h3><% _t('SilvercartOrderDetailPage.TITLE') %></h3>
        <table>
            <colgroup>
                <col width="35%"></col>
                <col width="65%"></col>
            </colgroup>
            <tbody>
                <tr>
                    <td><% _t('SilvercartPage.ORDER_DATE','Order date') %></td>
                    <td>$Created.Nice</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.PAYMENTMETHODTITLE','Payment method') %></td>
                    <td>$SilvercartPaymentMethod.Name</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.STATUS','Order status') %></td>
                    <td>$SilvercartOrderStatus.Title</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.SHIPPINGRATE','Shipping rate') %></td>
                    <td>$HandlingCostShipment.Nice</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.ORDER_VALUE','Orderamount') %></td>
                    <td>$AmountTotal.Nice</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></td>
                    <td>$OrderNumber</td>
                </tr>
                <% if Note %>
                <tr>
                    <td><% _t('SilvercartOrder.YOUR_REMARK','Your remark') %></td>
                    <td>$Note</td>
                </tr>
                <% end_if %>

                $OrderDetailInformation
            </tbody>
        </table>
    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
                <% control SilvercartInvoiceAddress %>
                    <% include SilvercartAddressDetailReadOnly %>
                <% end_control %>
            </div>
        </div>
        <div class="c50r">
            <div class="subcr">
                <% control SilvercartShippingAddress %>
                    <% include SilvercartAddressDetailReadOnly %>
                <% end_control %>
            </div>
        </div>
    </div>
    <h3><% _t('SilvercartOrderPosition.PLURALNAME','Order positions') %></h3>
    <table>
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
                <th class="right"><% _t('SilvercartPage.INCLUDED_VAT') %></th>
                <th class="right"><% _t('SilvercartProduct.QUANTITY') %></th>
                <th class="right"><% _t('SilvercartPrice.SINGULARNAME') %></th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>
<% end_control %>
