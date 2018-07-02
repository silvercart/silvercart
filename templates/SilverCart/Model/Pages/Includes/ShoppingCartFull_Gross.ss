<% if $CurrentPage.EditableShoppingCart %>
    <% if $CurrentMember.ShoppingCart.isFilled %>
<div class="btn-toolbar pull-right silvercart-shopping-cart-toolbar-top">
    <a class="btn" title="<%t SilverCart\Model\Pages\Page.CONTINUESHOPPING 'Continue shopping' %>" href="<% with $CurrentPage.PageByIdentifierCode(SilvercartFrontPage) %>{$Link}#1<% end_with %>">
        <i class="icon-caret-left"></i> <%t SilverCart\Model\Pages\Page.CONTINUESHOPPING 'Continue shopping' %>
    </a>
        <% if $CurrentMember.ShoppingCart.IsMinimumOrderValueReached %>
            <% with $CurrentPage.PageByIdentifierCode(SilvercartCheckoutStep) %>
    <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title}' title=$Title.XML %>" class="btn btn-primary">
        <%t SilverCart\Model\Pages\Page.CHECKOUT 'Checkout' %> <span class="icon-caret-right"></span>
    </a>
            <% end_with %>
        <% end_if %> 
</div>
    
<table class="table table-receipt top mobile-show-sm">
    <tr class="seperator"><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td>&nbsp;</td>
        <td class="text-right"><h2><%t SilverCart\Model\Pages\Page.TOTAL 'total' %></h2></td>
        <td class="text-right"><h2>$AmountTotalGross.Nice</h2></td>
    </tr>
</table>
    <% end_if %>
<% end_if %>

<%-- @TODO if screen.width  > 655px class="table = class="table-horizontal"  --%>
<table class="table silvercart-shopping-cart-full ">
    <% include SilverCart/Model/Pages/ShoppingCartFull_TableHead %>
    <tbody>
<% loop $ShoppingCartPositions %>
    <% include SilverCart/Model/Pages/ShoppingCartFull_Position %>
<% end_loop %> 
    </tbody>
</table>

<% if hasNotice %>
<p class="silvercart-message highlighted info16">
    $getShoppingCartPositionNotices
</p>
<% end_if %>

<div class="row-fluid">
<% if $CurrentPage.EditableShoppingCart %>
    <div class="span7 shoppingCartActions">
    <% if registeredModules %>
        <% loop registeredModules %>   
            <% if ShoppingCartActions %>
                <% loop ShoppingCartActions %>
                    $moduleOutput
                <% end_loop %>
            <% end_if %>
        <% end_loop %>
    <% end_if %> 
    </div>
    <div class="span5">
<% else %>
    <div class="span8 pull-right">
<% end_if %> 
        <div class="cart-receipt">
<%-- @TODO if screen.width  > 655px class="table-horizontal"  --%>
            <table class="table table-receipt">
<% if $registeredModules %>
    <% loop $registeredModules %>
        <% if $CustomShoppingCartPositions %>
            <% loop $CustomShoppingCartPositions %>
                {$CustomShoppingCartPosition}
            <% end_loop %>
        <% end_if %>
        <% if $TaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><strong><%t SilverCart\Model\Pages\Page.SUBTOTAL 'subtotal' %></strong></td>
                    <td class="text-right" id="Sum"><strong><% with $CurrentMember.ShoppingCart %>{$TaxableAmountGrossWithoutFeesAndChargesAndModules.Nice}<% end_with %></strong></td>
            <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
            <% end_if %>
                </tr>
            <% loop $TaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">{$Name}</td>
                    <td class="text-right">{$PriceFormatted}</td>
                    <td class="text-right">{$Tax.Title}</td> 
                    <td class="text-right">{$getTypeSafeQuantity}</td>
                    <td class="text-right">{$PriceTotalFormatted}</td>
                <% if $CurrentPage.EditableShoppingCart %>
                    <td>{$removeFromCartForm}</td>
                <% end_if %>
                </tr>
            <% end_loop %>
        <% end_if %>
    <% end_loop %>
<% end_if %>

<% if $HasFeesOrChargesOrModules %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><strong><%t SilverCart\Model\Pages\Page.VALUE_OF_GOODS 'Value of goods' %></strong></td>
                    <td class="text-right" id="Sum"><strong>{$TaxableAmountGrossWithoutFeesAndCharges.Nice}</strong></td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
<% end_if %>
<% if HasChargesAndDiscountsForProducts %>
    <% loop ChargesAndDiscountsForProducts %>
                <tr>
                    <td colspan="4">{$Name}</td>
                    <td class="text-right">{$Tax.Title}</td>
                    <td colspan="<% if $CurrentPage.EditableShoppingCart %>3<% else %>2<% end_if %>" class="text-right">{$PriceFormatted}</td>
                </tr>
    <% end_loop %>

                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><strong><%t SilverCart\Model\Pages\Page.SUBTOTAL 'subtotal' %></strong></td>
                    <td class="text-right" id="Sum"><strong>{$TaxableAmountGrossWithoutFees.Nice}</strong></td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
<% end_if %>

<% if HasFeesOrChargesOrModules %>
    <% if TaxRatesWithoutFees %>
        <% loop TaxRatesWithoutFees %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right">{$Amount.Nice}</td>
            <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
            <% end_if %>
                </tr>
        <% end_loop %>
    <% end_if %>
<% end_if %>

<% if $ShowFees %>
                <tr>
                    <td colspan="2">{$ShippingMethod.singular_name}:</td>
                    <td colspan="4" class="text-right"><strong>{$CarrierAndShippingMethodTitle} <% if $ShippingMethod.ShippingFee.PostPricing %>*<% end_if %></strong></td>
                    <td class="text-right">{$HandlingCostShipment.Nice}</td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
                <tr>
                    <td colspan="2">{$payment.singular_name}:</td>
                    <td colspan="4" class="text-right"><strong>{$payment.Name}</strong></td>
                    <td class="text-right">{$HandlingCostPayment.Nice}</td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
<% end_if %>

<% if $HasChargesAndDiscountsForTotal %>
    <% if $ShowFees %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><strong><%t SilverCart\Model\Pages\Page.SUBTOTAL 'Subtotal' %></strong></td>
                    <td class="text-right" id="Sum"><strong>{$TaxableAmountGrossWithFees.Nice}</strong></td>
        <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
        <% end_if %>
                </tr>
        <% if $TaxRatesWithFees %>
            <% loop $TaxRatesWithFees %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right">{$Amount.Nice}</td>
                <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
                <% end_if %>
                </tr>
            <% end_loop %>
        <% end_if %>
    <% end_if %>
    <% loop $ChargesAndDiscountsForTotal %>
                <tr>
                    <td colspan="4">{$Name}</td>
                    <td class="text-right">{$Tax.Title}</td>
                    <td colspan="<% if $CurrentPage.EditableShoppingCart %>3<% else %>2<% end_if %>" class="text-right">{$PriceFormatted}</td>
                </tr>
    <% end_loop %>

    <% loop $registeredModules %>
        <% if $NonTaxableShoppingCartPositions %>
            <% loop $NonTaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">{$Name}</td>
                    <td class="text-right">{$PriceFormatted}</td>
                    <td>&nbsp;</td>
                    <td class="text-right">{$getTypeSafeQuantity}</td>
                    <td class="text-right">{$PriceTotalFormatted}</td>
                <% if $CurrentPage.EditableShoppingCart %>
                     <td>{$removeFromCartForm}</td>
                <% end_if %>
                </tr>
            <% end_loop %>
        <% end_if %>
    <% end_loop %>

                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><h2><%t SilverCart\Model\Pages\Page.TOTAL 'total' %></h2></td>
                    <td class="text-right"><h2>{$AmountTotal.Nice}</h2></td>
    <% if $CurrentPage.EditableShoppingCart %>
                        <td>&nbsp;</td>
    <% end_if %>
                </tr>

    <% if TaxTotal %>
        <% loop TaxTotal %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right">{$Amount.Nice}</td>
            <% if $CurrentPage.EditableShoppingCart %>
                        <td>&nbsp;</td>
            <% end_if %>
                </tr>
        <% end_loop %>
    <% end_if %>

<% else %> 
    <%-- else of HasChargesAndDiscountsForTotal --%>

    <% loop $registeredModules %>
        <% if $NonTaxableShoppingCartPositions %>
            <% loop $NonTaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">{$Name}</td>
                    <td class="text-right">{$PriceFormatted}</td>
                    <td>&nbsp;</td>
                    <td class="text-right">{$getTypeSafeQuantity}</td>
                    <td class="text-right">{$PriceTotalFormatted}</td>
                    <td>$removeFromCartForm</td>
                </tr>
            <% end_loop %>
        <% end_if %>
    <% end_loop %>

                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><h2><%t SilverCart\Model\Pages\Page.TOTAL 'total' %></h2></td>
                    <td class="text-right"><h2>{$AmountTotal.Nice}<% if $ShippingMethod.ShippingFee.PostPricing %>*<% end_if %></h2></td>
                    <td>&nbsp;</td>
                </tr>

    <% if $TaxTotal %>
        <% loop $TaxTotal %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right"><%t SilverCart\Model\Pages\Page.INCLUDED_VAT 'included VAT' %> ({$Rate}%)</td>
                    <td class="text-right">{$Amount.Nice}</td>
            <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
            <% end_if %>
                </tr>
                <% end_loop %>
    <% end_if %>

    <% loop registeredModules %>
        <% if IncludedInTotalShoppingCartPositions %>
            <% loop IncludedInTotalShoppingCartPositions %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="text-right">{$Name}</td>
                    <td class="text-right">{$PriceTotalFormatted}</td>
                    <td>&nbsp;</td>
                </tr>
            <% end_loop %>
        <% end_if %>
    <% end_loop %>
<% end_if %>

<% if $addToEditableShoppingCartTable %>
    <% loop $addToEditableShoppingCartTable %>
                <tr>
                    <td colspan="3">{$TitleField}&nbsp;</td>
                    <td colspan="3" class="text-right">{$RightTitleField}&nbsp;</td>
                    <td class="text-right">{$PriceField.Nice}</td>
                    <td>&nbsp;</td>
                </tr>
    <% end_loop %>
<% end_if %>
            </table>
<% if $ShippingMethod.ShippingFee.PostPricing %>
            <div class="alert alert-block">
                <b>* <%t SilverCart\Model\Pages\Page.PLUS_SHIPPING 'plus shipping' %>, <%t SilverCart\Model\Shipment\ShippingFee.POST_PRICING_INFO 'Manual calculation of shipping fees after order.' %></b>
            </div>
<% end_if %>
        </div>
    </div>    <!-- close span5 / span8 -->
</div>
    
<% if $CurrentPage.EditableShoppingCart %>
    <% if $CurrentMember.ShoppingCart.isFilled %>
        <div class="row-fluid">
            <div class="btn-toolbar pull-right silvercart-shopping-cart-toolbar-bottom last">
                <a class="btn" title="<%t SilverCart\Model\Pages\Page.CONTINUESHOPPING 'Continue shopping' %>" href="<% with $CurrentPage.PageByIdentifierCode(SilvercartFrontPage) %>{$Link}#1<% end_with %>">
                    <i class="icon-caret-left"></i> <%t SilverCart\Model\Pages\Page.CONTINUESHOPPING 'Continue shopping' %>
                </a>
        <% if $CurrentMember.ShoppingCart.IsMinimumOrderValueReached %>
            <% with $CurrentPage.PageByIdentifierCode(SilvercartCheckoutStep) %>
                <a href="$Link" title="<%t SilverCart\Model\Pages\Page.GOTO 'go to {title}' title=$Title.XML %>" class="btn btn-primary">
                    <%t SilverCart\Model\Pages\Page.CHECKOUT 'Checkout' %> <i class="icon-caret-right"></i>
                </a>
            <% end_with %>
        <% end_if %> 
            </div>
        </div>
    <% end_if %>
<% end_if %>