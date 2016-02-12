<div class="row">
    $InsertWidgetArea(Content)
    <% include SilvercartShoppingCartFull %>
<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <% if CurrentMember.SilvercartShoppingCart.IsMinimumOrderValueReached %>
    <% else %>
    <div class="alert alert-info">
        <% sprintf(_t('SilvercartShoppingCart.ERROR_MINIMUMORDERVALUE_NOT_REACHED'),$SilvercartShoppingCart.MinimumOrderValue) %>
    </div>
    <% end_if %>
<% end_if %>
</div>