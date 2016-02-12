<div class="silvercart-checkout-shipping">
    <% if items %>
    <ul class="unstyled">
        <% loop items %>
        <li>
            <label class="radio" for="$htmlId">
                <input type="radio" name="$name" value="$value" id="$htmlId"<% if checked %> checked="checked"<% end_if %> />
                       $label                 
            </label>        
            <% if description %>
                <div class="alert alert-info silvercart-checkout-shipping-additionalInfo-description">
                    <p><i>$description</i></p>
                <% if ShippingMethod.DeliveryTime %>
                    <strong><i>$ShippingMethod.fieldLabel(ExpectedDelivery): {$ShippingMethod.DeliveryTime}</i></strong>
                <% end_if %>
                </div>
            <% else_if ShippingMethod.DeliveryTime %>
                <div class="alert alert-info silvercart-checkout-shipping-additionalInfo-description">
                    <strong><i>$ShippingMethod.fieldLabel(ExpectedDelivery): {$ShippingMethod.DeliveryTime}</i></strong>
                </div>
            <% end_if %>
        </li>
        <% end_loop %>
    </ul>
    <% else %>
    <div class="alert alert-error">
    <p><% _t('SilvercartShippingMethod.NO_SHIPPING_METHOD_AVAILABLE') %></p>
    </div>
    <% end_if %>
</div>
