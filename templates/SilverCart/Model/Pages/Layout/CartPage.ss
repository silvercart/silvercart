<% if $Checkout.ShowCartInCheckoutNavigation %>
    <% include SilverCart/Model/Pages/CheckoutStepNavigation %>
<% end_if %>
<div class="row-fluid">
    {$InsertWidgetArea(Content)}
    <% include SilverCart/Model/Pages/ShoppingCartFull %>
</div>
<% if $CurrentMember.ShoppingCart.isFilled && not $CurrentMember.ShoppingCart.IsMinimumOrderValueReached %>
<p class="alert alert-error"><%t SilverCart\Model\Order\ShoppingCart.ERROR_MINIMUMORDERVALUE_NOT_REACHED 'The minimum order value is {amount}' amount=$ShoppingCart.MinimumOrderValue %></p>
<% end_if %>