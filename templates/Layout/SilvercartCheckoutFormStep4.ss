{$Controller.ContentStep4}
<div class="form-horizontal grouped clearfix">
    <h4><% _t('SilvercartPaymentMethod.TITLE') %></h4>

    <% if RegisteredNestedForms %>
    <div class="silvercart-checkout-payment margin-side">
        <% loop RegisteredNestedForms %>
            {$InsertCustomHtmlForm}
        <% end_loop %>
    </div>
    <% else %>
    <div class="alert alert-error">
        <p><% _t('SilvercartPaymentMethod.NO_PAYMENT_METHOD_AVAILABLE') %></p>
    </div>
    <% end_if %>
</div>