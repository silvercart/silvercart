<% if $Checkout && $Checkout.CheckoutSteps %>
    <% with $Checkout %>
<table class="table checkout-steps">
    <tr>
    <% if $ShowCartInCheckoutNavigation %>
        <td <% if $CurrentPageIsCartPage %>class="current-step"<% end_if %>>
            <div class="well">
                <% if $CurrentPageIsCartPage %>
                <span class="highlight active"><strong><span class="icon-shopping-cart"></span> 1. <span class="step-title"><%t SilverCart\Model\Pages\Page.CART 'Cart' %></span></strong></span>
                <% else %>
                <a class="highlight" href="{$Top.PageByIdentifierCode(SilvercartCartPage).Link}"><span class="icon-ok"></span> <span class="icon-shopping-cart"></span> 1. <span class="step-title">{$CurrentUser.Cart.singular_name}</span></a>
                <% end_if %>
            </div>
        </td>
    <% end_if %>
    <% loop $CheckoutSteps %>
        <% if $IsVisible %>
        <td <% if $IsCurrentStep %>class="current-step"<% end_if %>>
            <div class="well">
                <% if $IsCurrentStep %>
                <span class="highlight active step-{$StepNumber}"><strong><span class="icon-checkoutstep{$StepNumber}"></span> {$VisibleStepNumber}. <span class="step-title">{$StepTitle}</span></strong></span>
                <% else_if $IsCompleted %>
                <a class="highlight" href="{$Top.Link(step)}/{$StepNumber}"><span class="icon-ok"></span> <span class="icon-checkoutstep{$StepNumber}"></span> {$VisibleStepNumber}. <span class="step-title">{$StepTitle}</span></a>
                <% else_if $IsPreviousStepCompleted %>
                <a class="highlight" href="{$Top.Link(step)}/{$StepNumber}"><span class="icon-ok"></span> <span class="icon-checkoutstep{$StepNumber}"></span> {$VisibleStepNumber}. <span class="step-title">{$StepTitle}</span></a>
                <% else %>
                <span><span class="icon-checkoutstep{$StepNumber}"></span> {$VisibleStepNumber}. <span class="step-title">{$StepTitle}</span></span>
                <% end_if %>
            </div>
        </td>
        <% end_if %>
    <% end_loop %>
    </tr>
</table>
    <% end_with %>
<% end_if %>