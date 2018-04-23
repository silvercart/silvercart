<% include SilverCart/Model/Pages/CheckoutStepNavigation %>
<div class="row">
    <div class="span12">
    <% if $canCheckout %>
        {$Content}
        {$Checkout.CurrentStep}
        <div class="silvercartWidgetHolder">
            {$InsertWidgetArea(Content)}
        </div>
    <% else %>
        <div class="alert alert-danger">{$CheckoutErrorMessage}</div>
    <% end_if %>
    </div>
</div>
