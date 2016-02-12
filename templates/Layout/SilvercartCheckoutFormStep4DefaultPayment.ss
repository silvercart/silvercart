<form class="form-horizontal" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    <% with PaymentMethod %>
    <div class="silvercart-checkout-payment-additionalInfo clearfix">
        <h2>$Name</h2>
        <% if showPaymentLogos %>
            <div class="silvercart-checkout-payment-additionalInfo-logos margin">
            <% if PaymentLogos %>
                <span class="silvercart-checkout-payment-additionalInfo-logo">
                    <% loop PaymentLogos %>
                        $Image.SetRatioSizeIfBigger(250,70)
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
    <% loop Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
    <% end_loop %>
    <hr>
</form>