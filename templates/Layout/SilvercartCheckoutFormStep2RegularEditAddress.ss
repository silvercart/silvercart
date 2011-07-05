<div id="col1">
    <div id="col1_content" class="clearfix">
        <h2>$Title</h2>
        $Content
        $Process
        $InsertCustomHtmlForm(SilvercartEditAddressForm)
<% control CurrentFormInstance %>
    <% if ShowCustomHtmlFormStepNavigation %>
        <% if Top.CustomHtmlFormStepLinkCancel %>
        <a class="silvercart-cancel-button" href="$Top.CustomHtmlFormStepLinkCancel"><% _t('SilvercartPage.CANCEL') %></a>
        <% end_if %>
    <% end_if %>
<% end_control %>

        $PageComments
    </div>
</div>

<div id="col3">
    <div id="col3_content" class="clearfix">

        <div class="widget">
            <div class="widget_content">
    <% control CurrentFormInstance %>
        <% if ShowCustomHtmlFormStepNavigation %>
                <strong><% _t('SilvercartPage.STEPS','steps') %></strong>
                <ul>
            <% control Top.StepList %>
                <% control step %>
                    <% if StepIsVisible %>
                    <li<% if IsCurrentStep %> class="active"<% end_if %>>
                        <% if IsCurrentStep %>
                        <p><% if StepImage %>$StepImage<% end_if %>$StepTitle</p>
                        <% else_if isStepCompleted %>
                        <a href="{$Top.Link}GotoStep/{$StepNr}"><% if StepImage %>$StepImage<% end_if %>$StepTitle</a>
                        <% else_if isPreviousStepCompleted %>
                        <a href="{$Top.Link}GotoStep/{$StepNr}"><% if StepImage %>$StepImage<% end_if %>$StepTitle</a>
                        <% else %>
                        <p><% if StepImage %>$StepImage<% end_if %>$StepTitle</p>
                        <% end_if %>
                    </li>
                    <% end_if %>
                <% end_control %>
            <% end_control %>
                </ul>
        <% end_if %>
    <% end_control %>

            </div>
        </div>

    </div>

     <!-- IE Column Clearing -->
    <div id="ie_clearing"> &#160; </div>
</div>
