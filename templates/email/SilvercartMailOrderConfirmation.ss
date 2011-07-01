<style type="text/css">
    table { width:auto; border-collapse:collapse; margin-bottom:0.5em; border-top:0px; border-bottom:0px; }
    table caption { font-variant:small-caps; }
    table.full { width:100%; }
    table.fixed { table-layout:fixed; }

    th,td { padding:0.5em; }
    thead th { border-bottom:1px #666 solid; }
    tbody th { background:#e0e0e0; color:#666; }
    tbody th[scope="row"], tbody th.sub { background:#f0f0f0; }

    tbody th { border-bottom:1px solid #fff; text-align:left; }
    tbody td { border-bottom:1px solid #eee; }
    tbody tr.new.block td {  }

    tfoot td {border-top: 1px #666 solid; }
</style>
<h1><% _t('SilvercartOrderConfirmationPage.ORDERCONFIRMATION') %></h1>

<p><% _t('SilvercartShopEmail.HELLO', 'Hello') %> $Salutation $FirstName $Surname,</p>
<p><% _t('SilvercartShopEmail.ORDER_ARRIVED', 'We confirm your order, many thanks.') %></p>
<p><% _t('SilvercartOrderDetailPage.TITLE') %>:</p>
<% control SilvercartOrder %>
<table>
    <tr>
        <td><% _t('SilvercartPage.ORDER_DATE') %></td>
        <td>$Created.Nice</td>
    </tr>
    <tr>
        <td><% _t('SilvercartNumberRange.ORDERNUMBER') %></td>
        <td>$OrderNumber</td>
    </tr>
    <tr>
        <td><% _t('SilvercartOrder.STATUS') %></td>
        <td>$SilvercartOrderStatus.Title</td>
    </tr>
    <% if Note %>
    <tr>
        <td><% _t('SilvercartOrder.YOUR_REMARK') %></td>
        <td>$FormattedNote</td>
    </tr>
    <% end_if %>
</table>

<table>
    <tbody>
        <tr>
            <td>
                <h2><% _t('SilvercartPage.SHIPPING_ADDRESS') %>:</h2>
                <% control SilvercartShippingAddress %>
                <table>
                    <tr>
                        <td><% _t('SilvercartAddress.FIRSTNAME') %></td>
                        <td>$FirstName</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.SURNAME') %></td>
                        <td>$Surname</td>
                    </tr>
                    <% if Addition %>
                    <tr>
                        <td><% _t('SilvercartAddress.ADDITION') %></td>
                        <td>$Addition</td>
                    </tr>
                    <% end_if %>
                    <tr>
                        <td><% _t('SilvercartAddress.STREET') %></td>
                        <td>$Street</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.STREETNUMBER') %></td>
                        <td>$StreetNumber</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.POSTCODE') %></td>
                        <td>$Postcode</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.CITY') %></td>
                        <td>$City</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.PHONE') %></td>
                        <td><% if Phone %>{$PhoneAreaCode}/{$Phone}<% else %>---<% end_if %></td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartCountry.SINGULARNAME') %></td>
                        <td>$SilvercartCountry.Title</td>
                    </tr>
                </table>
                <% end_control %>
            </td>
            <td>
                <h2><% _t('SilvercartInvoiceAddress.SINGULARNAME') %>:</h2>
                <% control SilvercartInvoiceAddress %>
                <table>
                    <tr>
                        <td><% _t('SilvercartAddress.FIRSTNAME') %></td>
                        <td>$FirstName</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.SURNAME') %></td>
                        <td>$Surname</td>
                    </tr>
                    <% if Addition %>
                    <tr>
                        <td><% _t('SilvercartAddress.ADDITION') %></td>
                        <td>$Addition</td>
                    </tr>
                    <% end_if %>
                    <tr>
                        <td><% _t('SilvercartAddress.STREET') %></td>
                        <td>$Street</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.STREETNUMBER') %></td>
                        <td>$StreetNumber</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.POSTCODE') %></td>
                        <td>$Postcode</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.CITY') %></td>
                        <td>$City</td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartAddress.PHONE') %></td>
                        <td><% if Phone %>{$PhoneAreaCode}/{$Phone}<% else %>---<% end_if %></td>
                    </tr>
                    <tr>
                        <td><% _t('SilvercartCountry.SINGULARNAME') %></td>
                        <td>$SilvercartCountry.Title</td>
                    </tr>
                </table>
                <% end_control %>
            </td>
        </tr>
    </tbody>
</table>

<h2><% _t('SilvercartPage.ORDERD_PRODUCTS') %>:</h2>
<table>
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
        <% control SilvercartOrderPositions(TaxRate > 0) %>
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

        <tr class="new-block">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
            <td class="right"><strong>$TaxableAmountGross.Nice</strong></td>
        </tr>

        <% if TaxRatesWithoutFees %>
        <% control TaxRatesWithoutFees %>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><% _t('SilvercartPage.INCLUDED_VAT') %> ({$Rate}%)</td>
            <td class="right">$Amount.Nice</td>
        </tr>
        <% end_control %>
        <% end_if %>

        <tr>
            <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %></td>
            <td colspan="3"><strong>$CarrierAndShippingMethodTitle</strong></td>
            <td class="right">$HandlingCostShipment.Nice</td>
        </tr>
        <tr>
            <td colspan="2"><% _t('SilvercartOrder.PAYMENTMETHODTITLE') %></td>
            <td colspan="3"><strong>$PaymentMethodTitle</strong></td>
            <td class="right">$HandlingCostPayment.Nice</td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="right"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
            <td class="right"><strong>$TaxableAmountGrossWithFees.Nice</strong></td>
        </tr>

        <% if TaxRatesWithFees %>
        <% control TaxRatesWithFees %>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><% _t('SilvercartPage.INCLUDED_VAT') %> ({$Rate}%)</td>
            <td class="right">$Amount.Nice</td>
        </tr>
        <% end_control %>
        <% end_if %>

        <% control SilvercartOrderPositions(TaxRate = 0) %>
        <tr>
            <td>&nbsp;</td>
            <td>$Title</td>
            <td class="right">$Price.Nice</td>
            <td class="right"></td>
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

<p><% _t('SilvercartShopEmail.REGARDS', 'Best regards') %>,</p>
<p><% _t('SilvercartShopEmail.YOUR_TEAM', 'Your SilverCart ecommerce team') %></p>