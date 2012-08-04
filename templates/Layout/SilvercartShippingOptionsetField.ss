
<div class="silvercart-checkout-shipping">
    <% if items %>
    <ul>
        <% loop items %>
            <li>
                <span class="silvercart-checkout-shipping-radiofield">
                    <input type="radio" name="$name" value="$value" id="$htmlId"<% if checked %> checked="checked"<% end_if %> />
                </span>
                <span class="silvercart-checkout-shipping-additionalInfo">
                    <label for="$htmlId">$label</label>
                    <% if description %>
                        <span class="silvercart-checkout-shipping-additionalInfo-description">
                            <i>$description</i>
                        </span>
                    <% end_if %>
                </span>
            </li>
        <% end_loop %>
    </ul>
    <% else %>
    <p><% _t('SilvercartShippingMethod.NO_SHIPPING_METHOD_AVAILABLE') %></p>
    <% end_if %>
</div>
