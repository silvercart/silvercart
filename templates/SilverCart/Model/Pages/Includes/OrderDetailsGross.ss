<table class="table silvercart-order-table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr class="mobile-hide-sm">
            <th class="text-left"><%t SilverCart\Model\Product\Product.PRODUCTNUMBER_SHORT 'Item no.' %></th>
            <th class="text-left"><%t SilverCart\Model\Product\Product.COLUMN_TITLE 'Title' %></th>
            <th class="text-right"><%t SilverCart\Model\Product\Product.PRICE_SINGLE 'Price single' %></th>
            <th class="text-right"><%t SilverCart\Model\Product\Product.VAT 'VAT' %></th>
            <th class="text-right"><%t SilverCart\Model\Product\Product.QUANTITY 'Quantity' %></th>
            <th class="text-right"><%t SilverCart\Model\Product\Product.PRICE 'Price' %></th>
        </tr>
        <tr class="mobile-show-sm">
            <th class="text-left" colspan="6">{$singular_name}</th>
        </tr>
    </thead>
    <tbody>
        <% loop $OrderListPositions %>
            <tr class="{$EvenOdd}">
                <td class="text-left productnumber">{$ProductNumber}</td>
                <td class="text-left producttitle">
                    <span class="title">{$Title.RAW}</span>
                    <% if $Product.exists %><a href="{$Product.Link}"><span class="icon icon-external-link"></span></a><% end_if %>
                    <% if $ShortDescription %><br/><span class="title-desc">{$ShortDescription.RAW}</span><% end_if %><% if $addToTitle %><br/><span class="title-add">{$addToTitle}</span><% end_if %></td>
                <td class="text-right sub-price">{$Price.Nice}</td>
                <td class="text-right vat">{$TaxRate}%<span class="mobile-show-sm inline"> <%t SilverCart\Model\Product\Product.VAT 'VAT' %></span></td>
                <td class="text-right quantity"><span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span></td>
                <td class="text-right total-price">{$PriceTotal.Nice}</td>
            </tr>
        <% end_loop %>

        <!-- sub total without fees and charges -->
        <% loop $getTaxableAmountGrossWithoutFees(false,false) %>
        <tr class="new-block">
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="desc-col"><strong><%t SilverCart\Model\Pages\Page.VALUE_OF_GOODS 'Value of goods' %></strong></td>
            <td class="text-right price-col">{$Amount.Nice}</td>
        </tr>
        <% end_loop %>

        <% if $HasChargePositionsForProduct %>
            <!-- charges and discounts for product value -->
            <% loop $OrderChargePositionsProduct %>
            <tr class="{$EvenOdd} auto">
                <td class="desc-col" colspan="2">{$Title.RAW}</td>
                <td class="text-right mobile-hide-sm">{$Price.Nice}</td>
                <td class="text-right mobile-hide-sm">{$TaxRate}%</td>
                <td class="text-right mobile-hide-sm"><span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span></td>
                <td class="text-right price-col">{$PriceTotal.Nice}</td>
            </tr>
            <% end_loop %>

            <!-- sub total without fees with product charges -->
            <% loop getTaxableAmountGrossWithoutFees(true,false) %>
                <tr class="new-block">
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="text-right desc-col"><strong><%t SilverCart\Model\Pages\Page.SUBTOTAL 'Subtotal' %></strong></td>
                    <td class="text-right price-col"><strong>{$Amount.Nice}</strong></td>
                </tr>
            <% end_loop %>

            <!-- tax rates for sub total without fees -->
            <% loop getTaxRatesWithoutFees(true,false) %>
                <tr class="new-block">
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="text-right desc-col" colspan="3"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right price-col">{$Amount.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <!-- fees -->
        <tr>
            <td class="pre-col" colspan="2">{$fieldLabel('ShippingMethod')}</td>
            <td class="desc-col" colspan="3"><strong>{$ShippingMethod.TitleWithCarrier} <% if $ShippingMethod.ShippingFee.PostPricing %>*<% end_if %></strong></td>
            <td class="text-right price-col">{$HandlingCostShipment.Nice}</td>
        </tr>
        <tr>
            <td class="pre-col" colspan="2">{$fieldLabel('PaymentMethodTitle')}</td>
            <td class="desc-col" colspan="3"><strong>{$PaymentMethod.Name}</strong></td>
            <td class="text-right price-col">{$HandlingCostPayment.Nice}</td>
        </tr>

        <% if $OrderChargePositionsTotal %>
            <!-- sub total -->
            <% loop getTaxableAmountGrossWithFees(true,false) %>
                <tr class="new-block">
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="text-right desc-col"><strong><%t SilverCart\Model\Pages\Page.SUBTOTAL 'Subtotal' %></strong></td>
                    <td class="text-right price-col"><strong>$Amount.Nice</strong></td>
                </tr>
            <% end_loop %>

            <!-- tax rates for sub total -->
            <% loop getTaxRatesWithFees(true,false) %>
                <tr class="new-block">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-right desc-col" colspan="3"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right price-col">{$Amount.Nice}</td>
                </tr>
            <% end_loop %>

            <!-- charges and discounts for the shopping cart value -->
            <% loop $OrderChargePositionsTotal %>
            <tr class="{$EvenOdd} auto">
                <td class="desc-col" colspan="2">{$Title.RAW}</td>
                <td class="text-right mobile-hide-sm">{$Price.Nice}</td>
                <td class="text-right mobile-hide-sm">{$TaxRate}%</td>
                <td class="text-right mobile-hide-sm"><span class="silvercart-quantity-label">{$getTypeSafeQuantity}<span class="mobile-show-sm inline">x</span></span></td>
                <td class="text-right price-col">{$PriceTotal.Nice}</td>
            </tr>
            <% end_loop %>
        <% end_if %>

        <tr>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="mobile-hide-sm">&nbsp;</td>
            <td class="text-right desc-col"><strong><%t SilverCart\Model\Pages\Page.TOTAL 'Total' %></strong></td>
            <td class="text-right price-col"><strong>$AmountTotal.Nice<% if $ShippingMethod.ShippingFee.PostPricing %>*<% end_if %></strong></td>
        </tr>
        <% if TaxTotal %>
            <% loop TaxTotal %>
                <tr>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="mobile-hide-sm">&nbsp;</td>
                    <td class="text-right desc-col" colspan="3"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right price-col">{$Amount.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>

        <% if $HasIncludedInTotalPositions %>
            <% loop $OrderIncludedInTotalPositions %>
                <tr class="{$EvenOdd}">
                    <td class="text-right desc-col" colspan="5">{$Title.RAW}</td>
                    <td class="text-right price-col">{$Price.Nice}</td>
                </tr>
            <% end_loop %>
        <% end_if %>
    </tbody>
</table>
<% if $ShippingMethod.ShippingFee.PostPricing %><b>* <%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %>, <%t SilverCart\Model\Shipment\ShippingFee.POST_PRICING_INFO 'Manual calculation of shipping fees after order.' %></b><% end_if %>
