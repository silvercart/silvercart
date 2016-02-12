<% if SilvercartErrors %>
<div class="alert alert-error">
    <a class="close" data-dismiss="alert">×</a>  
    $SilvercartErrors
</div>
<% end_if %>

<% if CurrentMember.SilvercartShoppingCart.isFilled %>

<% with CurrentMember %>
<% with SilvercartShoppingCart %>
<%-- @TODO if need diferent TOTAL Sum add Gross and Net template --%>
<% if $CurrentPage.showPricesGross %>
<% include SilvercartShoppingCartDropdownPosition %>
<% else %>
<% include SilvercartShoppingCartDropdownPosition %>
<% end_if %>

<% end_with %>
<% end_with %>
<% else %>
<!--div class="alert alert-error"-->
    <p><br />&nbsp;&nbsp;&nbsp;&nbsp;<% _t('SilvercartCartPage.CART_EMPTY', 'Your cart is empty') %></p>
<!--/div-->
<% end_if %>
