<% with CurrentFormInstance %>
    <% if ShowCustomHtmlFormStepNavigation %>
        <table class="table checkout-steps">
            <tr>
            <% loop Top.StepList %>
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
<div class="row">
    <div class="span12 clearfix">
        $Content
        $Process
        <br/>
        <a class="btn" href="{$CurrentPage.Link}">&ll; <% _t('SilvercartPage.BACK') %></a>
        $InsertCustomHtmlForm(SilvercartAddAddressForm)
    </div>
</div>