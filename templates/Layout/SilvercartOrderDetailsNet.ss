<table class="table silvercart-order-table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr class="mobile-hide-sm">
            <th class="align-left"><% _t('SilvercartProduct.PRODUCTNUMBER_SHORT') %></th>
            <th class="align-left"><% _t('SilvercartProduct.COLUMN_TITLE') %></th>
            <th class="align-right"><% _t('SilvercartProduct.PRICE_SINGLE') %></th>
            <th class="align-right"><% _t('SilvercartProduct.VAT') %></th>
            <th class="align-right"><% _t('SilvercartProduct.QUANTITY') %></th>
            <th class="align-right"><% _t('SilvercartPrice.SINGULARNAME') %></th>
        </tr>
        <tr class="mobile-show-sm">
            <th class="align-left" colspan="5">{$singular_name}</th>
        </tr>
    </thead>
    <tbody>
        <% loop SilvercartOrderListPositions %>
            <tr class="{$EvenOdd}">
                <td class="align-left productnumber">{$ProductNumber}</td>
                <td class="align-left producttitle"><span class="title">{$Title.RAW}</span><% if ShortDescription %><br/><span class="title-desc">{$ShortDescription.RAW}</span><% end_if %><% if addToTitle %><br/><span class="title-add">{$addToTitle}</span><% end_if %></td>
                <td class="align-right sub-price">{$Price.Nice}</td>
                <td class="align-right vat">{$TaxRate}%<span class="mobile-show-sm inline"> <% _t('SilvercartProduct.VAT') %></span></td>
                <td class="align-right quantity"><span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span></td>
                <td class="align-right total-price">{$PriceTotal.Nice}</td>
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
                            <strong class="hint"><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_PLURAL') %></strong>
                        <% else %>
                            <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERTITLE') %>
                            "<strong>$SilvercartVoucherCode</strong>".<br />
                            <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                            <strong class="hint"><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_SINGULAR') %></strong>
                        <% end_if %>
                    </td>
                </tr>
            <% end_if %>
        <% end_loop %>

        <% if HasChargePositionsForProduct %>
            <!-- charges and discounts for product value -->
            <% loop SilvercartOrderChargePositionsProduct %>
                <tr class="{$EvenOdd} auto">
                    <td class="desc-col" colspan="2">{$Title.RAW}</td>
                    <td class="align-right mobile-hide-sm">{$Price.Nice}</td>
                    <td class="align-right mobile-hide-sm">{$TaxRate}%</td>
                    <td class="align-right mobile-hide-sm"><span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span></td>
                    <td class="align-right price-col">{$PriceTotal.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <!-- sub total without fees with product charges -->
        <% loop getTaxableAmountNetWithoutFees(true,false) %>
            <tr class="new-block">
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="desc-col"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="align-right price-col">{$Amount.Nice}</td>
            </tr>
        <% end_loop %>

        <!-- fees -->
        <tr>
            <td class="pre-col" colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %></td>
            <td class="desc-col" colspan="3"><strong>{$SilvercartShippingMethod.TitleWithCarrier} <% with SilvercartShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
            <td class="align-right price-col">{$HandlingCostShipment.Nice}</td>
        </tr>
        <tr>
            <td class="pre-col" colspan="2"><% _t('SilvercartOrder.PAYMENTMETHODTITLE') %></td>
            <td class="desc-col" colspan="3"><strong>{$SilvercartPaymentMethod.Name}</strong></td>
            <td class="align-right price-col">{$HandlingCostPayment.Nice}</td>
        </tr>

        <!-- sub total -->
        <% loop getTaxableAmountNetWithFees(true,false) %>
            <tr class="new-block">
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="mobile-hide-sm">&nbsp;</td>
                <td class="desc-col"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                <td class="align-right price-col">$Amount.Nice</td>
            </tr>
        <% end_loop %>

        <!-- charges and discounts for the shopping cart value -->
        <% loop SilvercartOrderChargePositionsTotal %>
            <tr class="{$EvenOdd} auto">
                <td class="desc-col" colspan="2">{$Title.RAW}</td>
                <td class="align-right mobile-hide-sm">{$Price.Nice}</td>
                <td class="align-right mobile-hide-sm">{$TaxRate}%</td>
                <td class="align-right mobile-hide-sm"><span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span></td>
                <td class="align-right price-col">{$PriceTotal.Nice}</td>
            </tr>
        <% end_loop %>

        <% if TaxTotal %>
            <% loop TaxTotal %>
                <tr>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="align-right desc-col" colspan="3"><% _t('SilvercartPage.ADDITIONAL_VAT','Additional VAT') %> ({$Rate}%)</td>
                    <td class="align-right price-col">{$Amount.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <tr>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="align-right desc-col"><strong><% _t('SilvercartPage.TOTAL') %></strong></td>
            <td class="align-right price-col"><strong>$AmountTotal.Nice<% with SilvercartShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
        </tr>

        <% if HasIncludedInTotalPositions %>
            <% loop SilvercartOrderIncludedInTotalPositions %>
                <tr class="{$EvenOdd}">
                    <td class="align-right desc-col" colspan="5">{$Title.RAW}</td>
                    <td class="align-right price-col">{$Price.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>
    </tbody>
</table>
<% with SilvercartShippingMethod.ShippingFee %><% if PostPricing %><b>* <% _t('SilvercartPage.PLUS_SHIPPING') %>, <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></b><% end_if %><% end_with %>
