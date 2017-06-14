{$Controller.ContentStep4}
<div class="form-horizontal grouped clearfix">
    <h4><% _t('SilvercartPaymentMethod.TITLE') %></h4>
    <% if RegisteredNestedForms %>
        <% loop RegisteredNestedForms %>
            {$InsertCustomHtmlForm}
        <% end_loop %>
    <% else %>
    <div class="alert alert-error">
        <p><% _t('SilvercartPaymentMethod.NO_PAYMENT_METHOD_AVAILABLE') %></p>
    </div>
    <% end_if %>
</div>
{$CustomOutput}