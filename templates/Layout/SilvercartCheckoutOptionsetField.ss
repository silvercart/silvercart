<div class="silvercart-checkout-payment">
    <ul>
        <% control items %>
            <li>
                <span class="silvercart-checkout-payment-radiofield">
                    <input type="radio" name="$name" value="$value" />
                </span>
                <span class="silvercart-checkout-payment-additionalInfo">
                    <label>$label</label>

                    <i>Beschreibungstext...</i>

                    <span class="silvercart-checkout-payment-additionalInfo-icons">
                        <% control icons %>
                            
                        <% end_control %>
                    </span>
                </span>
            </li>
        <% end_control %>
    </ul>
</div>