<% if $ShopErrors %>
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert">×</a>  
        $ShopErrors
    </div>
<% end_if %>
<% if $CurrentMember.ShoppingCart.isFilled %>
    <% with $CurrentMember.ShoppingCart %>
        <% if $CurrentPage.showPricesGross %>
            <% include SilverCart/Model/Pages/ShoppingCartFull_Gross %>
        <% else %>
            <% include SilverCart/Model/Pages/ShoppingCartFull_Net %>
        <% end_if %>
    <% end_with %>
<% else %>
    <div class="alert alert-error">
        <p><br /><%t SilverCart\Model\Pages\CartPage.CART_EMPTY 'Your cart is empty' %></p>
    </div>
<% end_if %>
