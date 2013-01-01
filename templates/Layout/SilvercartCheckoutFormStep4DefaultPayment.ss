<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    <% with PaymentMethod %>
    <div class="silvercart-checkout-payment-additionalInfo">
        <strong>$Name</strong>
        <% if showPaymentLogos %>
            <div class="silvercart-checkout-payment-additionalInfo-logos">
            <% if PaymentLogos %>
                <span class="silvercart-checkout-payment-additionalInfo-logo">
                    <% loop PaymentLogos %>
                        $Image
                    <% end_loop %>
                </span>
            <% end_if %>
            </div>
        <% end_if %>
        <% if paymentDescription %>
            <div class="silvercart-checkout-payment-additionalInfo-description">
                <i>$paymentDescription.RAW</i>
            </div>
        <% end_if %>
    </div>
    <% end_with %>
    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
            $Field
            <% end_loop %>
        </div>
    </div>
</form>