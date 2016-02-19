<% if $CurrentPage.EditableShoppingCart %>
    <% if CurrentMember.SilvercartShoppingCart.isFilled %>
<div class="btn-toolbar pull-right silvercart-shopping-cart-toolbar-top">
    <a class="btn" title="<% _t('SilvercartPage.CONTINUESHOPPING') %>" href="<% with $CurrentPage.PageByIdentifierCode(SilvercartFrontPage) %>{$Link}#1<% end_with %>">
        <i class="icon-caret-left"></i> <% _t('SilvercartPage.CONTINUESHOPPING') %>
    </a>
        <% if CurrentMember.SilvercartShoppingCart.IsMinimumOrderValueReached %>
            <% with $CurrentPage.PageByIdentifierCode(SilvercartCheckoutStep) %>
    <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s'),$Title.XML) %>" class="btn btn-primary">
        <% _t('SilvercartPage.CHECKOUT') %> <i class="icon-caret-right"></i>
    </a>
            <% end_with %>
        <% end_if %> 
</div>
    
<table class="table table-receipt top mobile-show-sm">
    <tr class="seperator"><td colspan="3">&nbsp;</td></tr>
    <tr class="no-padding-bottom no-background-image">
        <td>&nbsp;</td>
        <td class="alignRight"><h2><% _t('SilvercartPage.TOTAL','total') %></h2></td>
        <td class="alignRight"><h2>$AmountTotalNet.Nice</h2></td>
    </tr>
        <% if TaxTotal %>
            <% loop TaxTotal %>
    <tr class="no-padding-top">
        <td>&nbsp;</td>
        <td class="alignRight"><% _t('SilvercartPage.INCLUDED_VAT') %> ({$Rate}%)</td>
        <td class="alignRight">$Amount.Nice<% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></td>
                <% if $CurrentPage.EditableShoppingCart %>
        <td>&nbsp;</td>
                <% end_if %>
    </tr>
            <% end_loop %>
        <% end_if %>
</table>
    <% end_if %>
<% end_if %>

<%-- @TODO if screen.width  > 655px class="table = class="table-horizontal"  --%>
<table class="table silvercart-shopping-cart-full ">
    <% include SilvercartShoppingCartFullTableHead %>
    <tbody>
<% loop SilvercartShoppingCartPositions %>
    <% include SilvercartShoppingCartFullPosition %>
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
<% if registeredModules %>
    <% loop registeredModules %>
        <% if CustomShoppingCartPositions %>
            <% loop CustomShoppingCartPositions %>
                {$CustomShoppingCartPosition}
            <% end_loop %>
        <% end_if %>
        <% if TaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                    <td class="alignRight" id="Sum"><strong><% with CurrentMember.SilvercartShoppingCart %>$TaxableAmountNetWithoutFeesAndChargesAndModules.Nice<% end_with %></strong></td>
            <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
            <% end_if %>
                </tr>
            <% loop TaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">$Name</td>
                    <td class="alignRight">$PriceNetFormatted</td>
                    <td class="alignRight">$Tax.Title</td>
                    <td class="alignRight">$getTypeSafeQuantity</td>
                    <td class="alignRight">$PriceNetTotalFormatted</td>
                <% if $CurrentPage.EditableShoppingCart %>
                    <td>$removeFromCartForm</td>
                <% end_if %>
                </tr>
            <% end_loop %>
        <% end_if %>
    <% end_loop %>
<% end_if %>

<% if HasChargesAndDiscountsForProducts %>
    <% loop ChargesAndDiscountsForProducts %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="alignRight">$SilvercartTax.Title</td>
                    <td colspan="<% if $CurrentPage.EditableShoppingCart %>3<% else %>2<% end_if %>" class="alignRight">$PriceFormatted</td>
                </tr>
    <% end_loop %>
<% end_if %>

                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                    <td class="alignRight" id="Sum"><strong>$TaxableAmountNetWithoutFees.Nice</strong></td>
<% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
<% end_if %>
                </tr>

<% if ShowFees %>
                <tr>
                    <td colspan="2"><% _t('SilvercartPaymentMethod.SHIPPINGMETHOD') %>:</td>
                    <td colspan="4" class="alignRight"><strong>$CarrierAndShippingMethodTitle <% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></strong></td>
                    <td class="alignRight">$HandlingCostShipment.Nice</td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
                <tr>
                    <td colspan="2"><% _t('SilvercartPaymentMethod.SINGULARNAME') %>:</td>
                    <td colspan="4" class="alignRight"><strong>$payment.Name</strong></td>
                    <td class="alignRight">$HandlingCostPayment.Nice</td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight"><strong><% _t('SilvercartPage.SUBTOTAL') %></strong></td>
                    <td class="alignRight" id="Sum"><strong>$TaxableAmountNetWithFees.Nice</strong></td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
<% end_if %>
        
<% loop registeredModules %>
    <% if NonTaxableShoppingCartPositions %>
        <% loop NonTaxableShoppingCartPositions %>
                <tr>
                    <td colspan="3">$Name</td>
                    <td class="alignRight">$PriceFormatted</td>
                    <td>&nbsp;</td>
                    <td class="alignRight">$getTypeSafeQuantity</td>
                    <td class="alignRight">$PriceTotalFormatted</td>
            <% if $CurrentPage.EditableShoppingCart %>
                    <td>$removeFromCartForm</td>
            <% end_if %>
                </tr>
        <% end_loop %>
    <% end_if %>
<% end_loop %>

<% if HasChargesAndDiscountsForTotal %>
    <% loop ChargesAndDiscountsForTotal %>
                <tr>
                    <td colspan="4">$Name</td>
                    <td class="right">$SilvercartTax.Title</td>
                    <td colspan="<% if $CurrentPage.EditableShoppingCart %>3<% else %>2<% end_if %>" class="right">$PriceFormatted</td>
                </tr>
    <% end_loop %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight"><strong><% _t('SilvercartPage.SUBTOTAL','subtotal') %></strong></td>
                    <td class="alignRight"><strong>$AmountTotalNetWithoutVat.Nice</strong></td>
    <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
    <% end_if %>
                </tr>
<% end_if %>

<% if TaxTotal %>
    <% loop TaxTotal %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight"><% _t('SilvercartPage.ADDITIONAL_VAT','Additional VAT') %> ({$Rate}%)</td>
                    <td class="alignRight">$Amount.Nice<% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %></td>
        <% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
        <% end_if %>
                </tr>
    <% end_loop %>
<% end_if %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight"><h2><% _t('SilvercartPage.TOTAL','total') %></h2></td>
                    <td class="alignRight"><h2>$AmountTotalNet.Nice</h2></td>
<% if $CurrentPage.EditableShoppingCart %>
                    <td>&nbsp;</td>
<% end_if %>
                </tr>

<% loop registeredModules %>
    <% if IncludedInTotalShoppingCartPositions %>
        <% loop IncludedInTotalShoppingCartPositions %>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="3" class="alignRight">$Name</td>
                    <td class="alignRight">{$PriceTotalFormatted}</td>
                    <td>&nbsp;</td>
                </tr>
        <% end_loop %>
    <% end_if %>
<% end_loop %>
        
<% if $CurrentPage.EditableShoppingCart %>
    <% if addToEditableShoppingCartTable %>
        <% with addToEditableShoppingCartTable %>
                <tr>
                    <td colspan="3">{$TitleField}&nbsp;</td>
                    <td colspan="3" class="alignRight">{$RightTitleField}&nbsp;</td>
                    <td class="alignRight">{$PriceField.Nice}</td>
                    <td>&nbsp;</td>
                </tr>
        <% end_with %>
    <% end_if %>
<% end_if %>
            </table>
<% with ShippingMethod.ShippingFee %><% if PostPricing %><div class="alert alert-block"><b>* <% _t('SilvercartPage.PLUS_SHIPPING') %>, <% _t('SilvercartShippingFee.POST_PRICING_INFO') %></b></div><% end_if %><% end_with %>
    <% if $CurrentPage.EditableShoppingCart %>
        <% if CurrentMember.SilvercartShoppingCart.isFilled %>
            <div class="btn-toolbar pull-right silvercart-shopping-cart-toolbar-bottom">
                <a class="btn" title="<% _t('SilvercartPage.CONTINUESHOPPING') %>" href="<% with $CurrentPage.PageByIdentifierCode(SilvercartFrontPage) %>{$Link}#1<% end_with %>">
                    <i class="icon-caret-left"></i> <% _t('SilvercartPage.CONTINUESHOPPING') %>
                </a>
            <% if CurrentMember.SilvercartShoppingCart.IsMinimumOrderValueReached %>
                <% with $CurrentPage.PageByIdentifierCode(SilvercartCheckoutStep) %>
                <a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s'),$Title.XML) %>" class="btn btn-primary">
                    <% _t('SilvercartPage.CHECKOUT') %> <i class="icon-caret-right"></i>
                </a>
                <% end_with %>
        <% end_if %> 
            </div>
    <% end_if %>
<% end_if %>
        </div>
    </div>
</div>