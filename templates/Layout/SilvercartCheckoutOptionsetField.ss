
<div class="silvercart-checkout-payment">
    <% if items %>
    <ul>
        <% control items %>
            <li>
                <span class="silvercart-checkout-payment-radiofield">
                    <input type="radio" name="$name" value="$value" id="$htmlId"<% if checked %> checked="checked"<% end_if %> />
                </span>
                <span class="silvercart-checkout-payment-additionalInfo">
                    <label for="$htmlId">$label</label>
                    <span class="silvercart-checkout-payment-additionalInfo-logos">
                    <% if showPaymentLogos %>
                        <% if PaymentLogos %>
                            <% control PaymentLogos %>
                                $Image
                            <% end_control %>
                        <% end_if %>
                    <% end_if %>
                    </span>
                    <span class="silvercart-checkout-payment-additionalInfo-description">
                    <% if description %>
                        <i>$description</i>
                    <% end_if %>
                    </span>
                </span>
            </li>
        <% end_control %>
    </ul>
    <% else %>
    <p>_t('SilvercartPaymentMethod.NO_PAYMENT_METHOD_AVAILABLE')</p>
    <% end_if %>
</div>
