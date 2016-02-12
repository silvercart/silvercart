{$Controller.ContentStep5}
<form class="form" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
<div class="row-fluid">
    <div class="span4">
    <% if hasOnlyOneStandardAddress %>
        <div class="checkout-change-area silvercart-highlighted-box well margin-bottom">
        <% with AddressData %>
            <% with SilvercartInvoiceAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_with %>
        <% end_with %>
            <a class="btn btn-small checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><i class="icon-edit"></i> <% _t('Silvercart.Change') %></a>
        </div>
    <% else %>              
        <% with AddressData %>
        <div class="checkout-change-area silvercart-highlighted-box well margin-bottom">
            <% with SilvercartInvoiceAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_with %>
            <a class="btn btn-small checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><i class="icon-edit"></i> <% _t('Silvercart.Change') %></a>
        </div>
        <div class="checkout-change-area silvercart-highlighted-box well margin-bottom">
            <% with SilvercartShippingAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_with %>
            <a class="btn btn-small checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><i class="icon-edit"></i> <% _t('Silvercart.Change') %></a>
        </div>
        <% end_with %>
    <% end_if %>
    
        <div class="silvercart-highlighted-box well checkout-change-area margin-bottom">
            <strong><% _t('SilvercartCheckoutFormStep.CHOOSEN_SHIPPING') %>:</strong>
            <p class="silvercart-highlighted-content">
            <% with SilvercartShoppingCart %>
                {$CarrierAndShippingMethodTitle} <% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %>
                <% if hasHandlingCostShipment %> ({$HandlingCostShipment.Nice})<% end_if %>
                <% if ShippingMethod.DeliveryTime %><br/><small class="delivery-time-hint">$ShippingMethod.fieldLabel(ExpectedDelivery):<br/>{$ShippingMethod.DeliveryTime}</small><% end_if %>
            <% end_with %>
            </p>
            <a class="btn checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.ShipmentStepNumber}"><i class="icon-edit"></i> <% _t('Silvercart.Change') %></a>
        </div>
        <div class="silvercart-highlighted-box well checkout-change-area margin-bottom">
            <strong><% _t('SilvercartCheckoutFormStep.CHOOSEN_PAYMENT') %>:</strong>
            <p class="silvercart-highlighted-content">
            <% with SilvercartShoppingCart %>
                {$payment.Name}
                <% if hasHandlingCostPayment %> ({$HandlingCostPayment.Nice})<% end_if %>
            <% end_with %>
            </p>
            <a class="btn checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.PaymentStepNumber}"><i class="icon-edit"></i> <% _t('Silvercart.Change') %></a>
        </div>
        <div class="silvercart-highlighted-box well small-well small-well-side margin-bottom" id="silvercart-checkout-note">
            <label><% _t('SilvercartPage.REMARKS') %></label>
            $CustomHtmlFormFieldByName(Note)
        </div>
    </div>
    <div class="span8">
        <div id="silvercart-checkout-privacy-check" class="well small-well margin-bottom">
            $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,SilvercartHasAcceptedTermsAndConditionsFieldCheck)
            $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,SilvercartHasAcceptedRevocationInstructionFieldCheck)
            <% if Top.showNewsletterCheckbox %>
            $CustomHtmlFormFieldByName(SubscribedToNewsletter,SilvercartHasAcceptedNewsletterFieldCheck)
            <% end_if %>
        </div>
        {$Top.getSilvercartShoppingCartFull}
        
        <hr>
        <% with CurrentPage %>
            <% if SilvercartConfig.ShowTaxAndDutyHint %>
        <p class="tax-and-duty-hint"><% _t('Silvercart.TaxAndDutyHint') %></p>
        <hr>
            <% end_if %>
        <% end_with %>
        <div class="margin-side clearfix">
        <% loop Actions %>
            <button type="submit" title="{$Title}" name="{$Name}" id="{$ID}" class="btn btn-primary btn-large btn-block-sm pull-right action">{$Title} <i class="icon icon-caret-right"></i></button>
        <% end_loop %>
        </div>
    </div>
</div>
</form>
