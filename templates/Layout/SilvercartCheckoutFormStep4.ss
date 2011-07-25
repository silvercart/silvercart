<div class="yform">
    <fieldset>
          <legend><% _t('SilvercartPaymentMethod.TITLE') %></legend>
          <div class="subcolumns">
            <div class="silvercart-checkout-payment">
                <% if RegisteredNestedForms %>
                <ul>
                    <% control RegisteredNestedForms %>
                    <li>
                        $InsertCustomHtmlForm
                    </li>
                    <% end_control %>
                </ul>
                <% else %>
                <p><% _t('SilvercartPaymentMethod.NO_PAYMENT_METHOD_AVAILABLE') %></p>
                <% end_if %>
            </div>
          </div>
    </fieldset>
</div>