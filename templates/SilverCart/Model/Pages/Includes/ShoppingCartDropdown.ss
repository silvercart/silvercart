<% if $ShopErrors %>
<div class="alert alert-error">
    <a class="close" data-dismiss="alert">×</a>
    {$ShopErrors}
</div>
<% end_if %>
<% if $CurrentMember.ShoppingCart.isFilled %>
    <% with $CurrentMember.ShoppingCart %>
        <% include SilverCart/Model/Pages/ShoppingCartDropdownPosition %>
    <% end_with %>
<% else %>
<div class="alert alert-info"><span class="fa fa-info-sign"></span> <%t SilverCart\Model\Pages\CartPage.CART_EMPTY 'Your cart is empty' %></div>
<% end_if %>