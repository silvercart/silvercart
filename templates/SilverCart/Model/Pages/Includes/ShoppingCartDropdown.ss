<% if $ShopErrors %>
<div class="alert alert-error">
    <a class="close" data-dismiss="alert">×</a>  
    $ShopErrors
</div>
<% end_if %>

<% if $CurrentMember.ShoppingCart.isFilled %>
    <% with $CurrentMember.ShoppingCart %>
        <% include SilverCart/Model/Pages/ShoppingCartDropdownPosition %>
    <% end_with %>
<% else %>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <%t SilverCart\Model\Pages\CartPage.CART_EMPTY 'Your cart is empty' %></p>
<% end_if %>