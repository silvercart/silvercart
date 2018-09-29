<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <div class="row-fluid">
        <div class="span4">
<%-- ------------------------------------------------------------------------------------------ --%>
<%-- ------------------------------------------------------------------------------------------ --%>
<%--                                      ADDRESS SECTION                                       --%>
<%-- ------------------------------------------------------------------------------------------ --%>
<%-- ------------------------------------------------------------------------------------------ --%>
    <% with $Controller.Checkout.CurrentStep %>
        <% if $InvoiceAddressIsShippingAddress %>
            <div class="checkout-change-area silvercart-highlighted-box well margin-bottom">
            {$BeforeInvoiceAddressContent}
            <% with $InvoiceAddress %>
                <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
            <% end_with %>
            {$AfterInvoiceAddressContent}
                <a class="btn btn-small checkout-change-button" href="{$Controller.AddressStepLink}"><span class="icon-edit"></span> <%t SilverCart\Model\Pages\CheckoutStep.Change 'Change' %></a>
            </div>
        <% else %>
            <div class="checkout-change-area silvercart-highlighted-box well margin-bottom">
                {$BeforeInvoiceAddressContent}
                <% with $InvoiceAddress %>
                    <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
                <% end_with %>
                {$AfterInvoiceAddressContent}
                <a class="btn btn-small checkout-change-button" href="{$Controller.AddressStepLink}"><span class="icon-edit"></span> <%t SilverCart\Model\Pages\CheckoutStep.Change 'Change' %></a>
            </div>
            <div class="checkout-change-area silvercart-highlighted-box well margin-bottom">
                {$BeforeShippingAddressContent}
                <% with $ShippingAddress %>
                    <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
                <% end_with %>
                {$AfterShippingAddressContent}
                <a class="btn btn-small checkout-change-button" href="{$Controller.AddressStepLink}"><span class="icon-edit"></span> <%t SilverCart\Model\Pages\CheckoutStep.Change 'Change' %></a>
            </div>
        <% end_if %>
    <% end_with %>
<%-- ------------------------------------------------------------------------------------------ --%>
<%-- ------------------------------------------------------------------------------------------ --%>
<%--                                SHIPMENT & PAYMENT SECTION                                  --%>
<%-- ------------------------------------------------------------------------------------------ --%>
<%-- ------------------------------------------------------------------------------------------ --%>
    <% with $Controller.Checkout.CurrentStep %>
            <div class="silvercart-highlighted-box well checkout-change-area margin-bottom">
                <strong><%t SilverCart\Model\Pages\CheckoutStep.CHOSEN_SHIPPING 'Chosen shipping method' %>:</strong>
                <p class="silvercart-highlighted-content">
                {$BeforeShippingMethodContent}
                <% with $ShippingMethod %>
                    {$Carrier.Title} - {$Title} <% if $ShippingFee.PostPricing %>*<% end_if %>
                    <% if $ShippingFee.CalculatedPrice %> ({$ShippingFee.CalculatedPrice.Nice})<% end_if %>
                <% end_with %>
                <% if $ShoppingCart.getDeliveryTime($ShippingMethod.ID) %>
                    <% if $ShippingMethod.isPickup %>
                    <br/><small class="delivery-time-hint">{$ShippingMethod.fieldLabel(ReadyForPickup)}:<br/>{$ShoppingCart.getDeliveryTime($ShippingMethod.ID)}</small>
                    <% else %>
                    <br/><small class="delivery-time-hint">{$ShippingMethod.fieldLabel(ExpectedDelivery)}:<br/>{$ShoppingCart.getDeliveryTime($ShippingMethod.ID)}</small>
                    <% end_if %>
                <% end_if %>
                {$AfterShippingMethodContent}
                </p>
                <% if not $Controller.SkipShippingStep %>
                <a class="btn checkout-change-button" href="{$Controller.ShipmentStepLink}"><span class="icon-edit"></span> <%t SilverCart\Model\Pages\CheckoutStep.Change 'Change' %></a>
                <% end_if %>
            </div>
            <div class="silvercart-highlighted-box well checkout-change-area margin-bottom">
                <strong><%t SilverCart\Model\Pages\CheckoutStep.CHOSEN_PAYMENT 'Chosen payment method' %>:</strong>
                <p class="silvercart-highlighted-content">
                {$BeforePaymentMethodContent}
                <% with $PaymentMethod %>
                    {$Name}
                    <% if $HandlingCost.amount.Amount > 0 %> ({$HandlingCost.amount.Nice})<% end_if %>
                <% end_with %>
                {$AfterPaymentMethodContent}
                </p>
                <% if not $Controller.SkipPaymentStep %>
                <a class="btn checkout-change-button" href="{$Controller.PaymentStepLink}"><span class="icon-edit"></span> <%t SilverCart\Model\Pages\CheckoutStep.Change 'Change' %></a>
                <% end_if %>
            </div>
    <% end_with %>
    <%-- ------------------------------------------------------------------------------------------ --%>
    <%-- ------------------------------------------------------------------------------------------ --%>
    <%--                                    FORM FIELDS SECTION                                     --%>
    <%-- ------------------------------------------------------------------------------------------ --%>
    <%-- ------------------------------------------------------------------------------------------ --%>
            <div class="silvercart-highlighted-box well small-well small-well-side margin-bottom" id="silvercart-checkout-note">
                <label><%t SilverCart\Model\Pages\Page.REMARKS 'Remarks' %></label>
                {$Fields.dataFieldByName(Note).FieldHolder}
            </div>
        </div>
        <div class="span8">
            <% if $ShowNewsletterCheckbox %>
            <div class="well small-well margin-bottom">
                {$Fields.dataFieldByName(SubscribedToNewsletter).FieldHolder}
            </div>
            <% end_if %>

            {$Controller.Checkout.CurrentStep.ShoppingCartFull}

            <hr/>
            <% if $CurrentPage.SiteConfig.ShowTaxAndDutyHint %>
            <p class="tax-and-duty-hint"><%t SilverCart\Model\Pages\CheckoutStep.TaxAndDutyHint 'Caution: There are additional taxes and fees for delivery to non EU countries.' %></p>
            <hr/>
            <% end_if %>
            <div class="margin-side clearfix">
            <% loop $Actions %>
                <button class="btn btn-primary btn-large btn-block-sm pull-right action" type="submit" title="{$Title}" name="{$Name}" id="{$ID}">{$Title} <span class="icon icon-caret-right"></span></button>
            <% end_loop %>
            </div>
            <p>{$AcceptTermsAndConditionsText}</p>
        </div>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>
