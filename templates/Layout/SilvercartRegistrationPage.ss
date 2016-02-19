<div class="row">
<% if IsInCheckout %>
    <div class="span12 clearfix">
<% else %>
    <div class="span9">
<% end_if %>
        <% if IsInCheckout %>
            <% with CheckoutFormInstance %>
                <% if ShowCustomHtmlFormStepNavigation %>
                <table class="table checkout-steps">
                    <tr>
                    <% loop Controller.StepList %>
                        <% with step %>
                            <% if StepIsVisible %>
                        <td<% if IsCurrentStep %> class="current-step"<% end_if %>>
                            <div class="well">
                                <% if IsCurrentStep %>
                                <span class="highlight active step-{$StepNr}"><strong><i class="icon-{$class}"></i> {$StepNr}. <span class="step-title">{$StepTitle.HTML}</span></strong></span>
                                <% else_if isStepCompleted %>
                                <a class="highlight" href="{$Top.Link}GotoStep/{$StepNr}"><i class="icon-ok"></i> <i class="icon-{$class}"></i> {$StepNr}. <span class="step-title">{$StepTitle.HTML}</span></a>
                                <% else_if isPreviousStepCompleted %>
                                <a class="highlight" href="{$Top.Link}GotoStep/{$StepNr}"><i class="icon-ok"></i> <i class="icon-{$class}"></i> {$StepNr}. <span class="step-title">{$StepTitle.HTML}</span></a>
                                <% else %>
                                <span><i class="icon-{$class}"></i> {$StepNr}. <span class="step-title">{$StepTitle.HTML}</span></span>
                                <% end_if %>
                            </div>
                        </td>
                            <% end_if %>
                        <% end_with %>
                    <% end_loop %>
                    </tr>
                </table>
                <% end_if %>
            <% end_with %>
        <% else %>
            <div class="section-header clearfix">
                <h1>{$Title}</h1>
            </div>
        <% end_if %>
        {$Content}
        <% if CurrentRegisteredCustomer %>
			<p><% sprintf(_t('SilvercartPage.ALREADY_REGISTERED','Hello %s, You have already registered.'),$CurrentMember.FirstName) %></p>
        <% else %>
			$InsertCustomHtmlForm(SilvercartRegisterRegularCustomerForm)
        <% end_if %>
    </div>
<% if not IsInCheckout %>
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
    </aside>
<% end_if %>
</div>
