{$Controller.ContentStep4}
<div class="form-horizontal clearfix">
    <h4><%t SilverCart\Model\Payment\PaymentMethod.TITLE 'Payment method' %></h4>
    <% if $AllowedPaymentMethods %>
        <% loop $AllowedPaymentMethods %>
            {$CheckoutChoosePaymentMethodForm}
        <% end_loop %>
    <% else %>
    <div class="alert alert-error">
        <p><%t SilverCart\Model\Payment\PaymentMethod.NO_PAYMENT_METHOD_AVAILABLE 'No payment method available.' %></p>
    </div>
    <div class="alert alert-error">{$CurrentPage.NoPaymentMethodText}</div>
    <% end_if %>
</div>
{$CustomOutput}