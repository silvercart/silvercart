<div id="col1">
    <div id="col1_content" class="clearfix">
        <h2>$Title</h2>
        $Content
        $Process
        $insertCustomHtmlForm
        <% if CustomHtmlFormStepLinkCancel %>
            <a href="$CustomHtmlFormStepLinkCancel"><% _t('SilvercartPage.CANCEL') %></a>
        <% end_if %>

        $PageComments
    </div>
</div>

<div id="col3">
    <div id="col3_content" class="clearfix">

        <div class="widget">
            <div class="widget_content">
                <strong><% _t('SilvercartPage.STEPS','steps') %></strong>

                <ul>
            <% control StepList %>
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

            </div>
        </div>

    </div>

     <!-- IE Column Clearing -->
    <div id="ie_clearing"> &#160; </div>
</div>
