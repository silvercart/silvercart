<div class="row-fluid">
    $InsertWidgetArea(Content)
    <% include SilvercartShoppingCartFull %>
</div>
<% if CurrentMember.SilvercartShoppingCart.isFilled %>
    <% if CurrentMember.SilvercartShoppingCart.IsMinimumOrderValueReached %>
    <% else %>
        <div class="row-fluid">
            <div class="span12 last alert alert-error">
                <% sprintf(_t('SilvercartShoppingCart.ERROR_MINIMUMORDERVALUE_NOT_REACHED'),$SilvercartShoppingCart.MinimumOrderValue) %>
            </div>
        </div>
    <% end_if %>
<% end_if %>