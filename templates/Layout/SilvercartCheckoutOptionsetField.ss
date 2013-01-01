
<div class="silvercart-checkout-payment">
    <% if items %>
    <ul>
        <% loop items %>
            <li>
                <span class="silvercart-checkout-payment-radiofield">
                    <input type="radio" name="$name" value="$value" id="$htmlId"<% if checked %> checked="checked"<% end_if %> />
                </span>
                <span class="silvercart-checkout-payment-additionalInfo">
                    <label for="$htmlId">$label</label>
                    <% if showPaymentLogos %>
                        <% if PaymentLogos %>
                            <span class="silvercart-checkout-payment-additionalInfo-logos">
                                <% loop PaymentLogos %>
                                    $Image
                                <% end_loop %>
                            </span>
                        <% end_if %>
                    <% end_if %>
                    <% if description %>
                        <span class="silvercart-checkout-payment-additionalInfo-description">
                            <i>$description.RAW</i>
                        </span>
                    <% end_if %>
                </span>
            </li>
        <% end_loop %>
    </ul>
    <% else %>
    <p><% _t('SilvercartPaymentMethod.NO_PAYMENT_METHOD_AVAILABLE') %></p>
    <% end_if %>
</div>
