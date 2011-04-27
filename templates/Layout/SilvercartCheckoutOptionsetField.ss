<% if items %>
    <div class="silvercart-checkout-payment">
        <ul>
            <% control items %>
                <li>
                    <span class="silvercart-checkout-payment-radiofield">
                        <input type="radio" id="$htmlId" name="$name" value="$value" />
                    </span>
                    <span class="silvercart-checkout-payment-additionalInfo">
                        <label for="$htmlId">$label</label>

                        <i>$description</i>

                        <% if icons %>
                            <span class="silvercart-checkout-payment-additionalInfo-icons">
                                <% control icons %>
                                    
                                <% end_control %>
                            </span>
                        <% end_if %>
                    </span>
                </li>
            <% end_control %>
        </ul>
    </div>
<% end_if %>