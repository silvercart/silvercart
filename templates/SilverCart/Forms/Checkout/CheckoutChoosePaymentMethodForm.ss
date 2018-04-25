<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>

<% with $PaymentMethod %>
    <div class="silvercart-checkout-payment-additionalInfo clearfix">
        <h2>{$Name}</h2>
    <% if $showPaymentLogos && $PaymentLogos.exists %>
        <div class="logos margin">
            <span class="logo">
                <% loop $PaymentLogos %>
                    {$Image.Pad(250,70)}
                <% end_loop %>
            </span>
        </div>
    <% end_if %>
    <% if $paymentDescription %>
        <div class="silvercart-checkout-payment-additionalInfo-description"><i>{$paymentDescription.RAW}</i></div>
    <% end_if %>
    </div>
<% end_with %>
<% loop $Actions %>
    <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
<% end_loop %>
    <hr>
    
<% if $IncludeFormTag %>
</form>
<% end_if %>
