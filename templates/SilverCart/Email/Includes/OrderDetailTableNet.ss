<table class="table silvercart-order-table">
    <tbody>
        <% loop $OrderListPositions %>
            <tr>
                <td style="padding: 1.2em 0.5em; white-space: nowrap; text-align: right; vertical-align: top;">{$getTypeSafeQuantity}x</td>
                <td style="padding: 1.2em 0.5em; width: 100%;">
                    <% if $Product.exists %>
                        <a style="float: left; margin-right: 8px;" href="{$Product.Link}"><img src="{$Product.ListImage.Pad(60,60).URL}" alt="" /></a> <a style="font-size: 1.1em;" href="{$Product.Link}">{$Title.RAW}</a>
                    <% else %>
                        <span style="font-size: 1.1em;">{$Title.RAW}</span>
                    <% end_if %>
                    <% if $ShortDescription %><br/><span style="">{$ShortDescription}</span><% end_if %>
                    <% if $addToTitle %><br/><span style="">{$addToTitle}</span><% end_if %></td>
                <td style="padding: 1.2em 0.5em; white-space: nowrap; text-align: right; vertical-align: top;">{$PriceTotal.Nice}</td>
            </tr>
        <% end_loop %>

        <% if $HasChargePositionsForProduct %>
            <%-- charges and discounts for product value --%>
            <% loop $OrderChargePositionsProduct %>
            <tr>
                <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$getTypeSafeQuantity}x</td>
                <td style="border: none; padding: 1.0em 0.5em 0.5em;">{$Title.RAW}</td>
                <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$PriceTotal.Nice}</td>
            </tr>
            <% end_loop %>
        <% end_if %>

        <%-- sub total without fees and charges --%>
        <% with $getTaxableAmountNetWithoutFees(true,false) %>
            <tr>
                <td style="text-align: right; border: none; border-top: 3px solid #dddddd; padding: 1.0em 0.5em 0.5em;" colspan="2"><%t SilverCart\Model\Pages\Page.SUBTOTAL 'Subtotal' %></td>
                <td style="text-align: right; border: none; border-top: 3px solid #dddddd; padding: 1.0em 0.5em 0.5em;">{$Amount.Nice}</td>
            </tr>
        <% end_with %>

        <%-- fees --%>
        <tr>
            <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;" colspan="2">{$fieldLabel('ShippingMethod')}: {$ShippingMethod.TitleWithCarrier} <% if $ShippingMethod.ShippingFee.PostPricing %>*<% end_if %></td>
            <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$HandlingCostShipment.Nice}</td>
        </tr>
        <% if $HandlingCostPayment.Amount > 0 %>
        <tr>
            <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;" colspan="2">{$fieldLabel('PaymentMethodTitle')}: {$PaymentMethod.Name}</td>
            <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$HandlingCostPayment.Nice}</td>
        </tr>
        <% end_if %>

        <%-- charges and discounts for the shopping cart value --%>
        <% loop $OrderChargePositionsTotal %>
            <tr>
                <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$getTypeSafeQuantity}x</td>
                <td style="border: none; padding: 1.0em 0.5em 0.5em;">{$Title.RAW}</td>
                <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$PriceTotal.Nice}</td>
            </tr>
        <% end_loop %>

        <% if $TaxTotal %>
            <% loop $TaxTotal %>
                <tr>
                    <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;" colspan="2"><%t SilverCart\Model\Pages\Page.ADDITIONAL_VAT 'Additional VAT' %> ({$Rate}%)</td>
                    <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$Amount.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <tr>
            <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;" colspan="2"><strong><%t SilverCart\Model\Pages\Page.TOTAL 'Total' %></strong></td>
            <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em; white-space: nowrap;"><strong>{$AmountTotal.Nice}<% if $ShippingMethod.ShippingFee.PostPricing %>*<% end_if %></strong></td>
        </tr>

        <% if $HasIncludedInTotalPositions %>
            <% loop $OrderIncludedInTotalPositions %>
                <tr class="{$EvenOdd}">
                    <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;" colspan="2">{$Title.RAW}</td>
                    <td style="text-align: right; border: none; padding: 1.0em 0.5em 0.5em;">{$Price.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>
    </tbody>
</table>
<% if $ShippingMethod.ShippingFee.PostPricing %><b>* <%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %>, <%t SilverCart\Model\Shipment\ShippingFee.POST_PRICING_INFO 'Manual calculation of shipping fees after order.' %></b><% end_if %>
