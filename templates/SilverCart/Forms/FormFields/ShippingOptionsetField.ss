<div class="silvercart-checkout-shipping">
    <% if $items %>
    <ul class="unstyled">
        <% loop $items %>
        <li>
            <label class="radio" for="{$htmlId}"><input type="radio" name="{$name}" value="{$value}" id="{$htmlId}"<% if checked %> checked="checked"<% end_if %> /> {$label}</label>        
            <% if $description || $CurrentUser.Cart.getDeliveryTime($ShippingMethod.ID) %>
                <div class="alert alert-info silvercart-checkout-shipping-additionalInfo-description">
                <% if $description %>
                    <p><i>{$description}</i></p>
                <% end_if %>
                <% if $CurrentUser.Cart.getDeliveryTime($ShippingMethod.ID) %>
                    <% if $ShippingMethod.isPickup %>
                    <strong><i>{$ShippingMethod.fieldLabel(ReadyForPickup)}: {$CurrentUser.Cart.getDeliveryTime($ShippingMethod.ID)}</i></strong>
                    <% else %>
                    <strong><i>{$ShippingMethod.fieldLabel(ExpectedDelivery)}: {$CurrentUser.Cart.getDeliveryTime($ShippingMethod.ID)}</i></strong>
                    <% end_if %>
                <% end_if %>
                </div>
            <% end_if %>
        </li>
        <% end_loop %>
    </ul>
    <% else %>
    <div class="alert alert-error"><%t SilverCart\Model\Shipment\ShippingMethod.NO_SHIPPING_METHOD_AVAILABLE 'No shipping method available' %></div>
    <% end_if %>
</div>
