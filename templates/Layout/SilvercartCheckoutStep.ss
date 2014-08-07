<div id="col1">
    <div id="col1_content" class="clearfix">
        <h1>$CurrentFormInstance.StepTitle.HTML</h1>
        $Content
        $Process
        $insertCustomHtmlForm
        
        <% control CurrentFormInstance %>
            <% if ShowCustomHtmlFormStepNavigation %>
                <% if Top.CustomHtmlFormStepLinkCancel %>
                    <div class="silvercart-button left">
                        <div class="silvercart-button_content">
                            <a href="$Top.CustomHtmlFormStepLinkCancel"><% _t('SilvercartPage.CANCEL') %></a>
                        </div>
                    </div>
                <% end_if %>
            <% end_if %>
        <% end_control %>

        $PageComments
    </div>
</div>

<div id="col3">
    <div id="col3_content" class="clearfix">

        <% control CurrentFormInstance %>
            <% if ShowCustomHtmlFormStepNavigation %>
                <div class="silvercart-widget">
                    <div class="silvercart-widget_content">
                
                        <strong class="h2"><% _t('SilvercartPage.STEPS','steps') %></strong>
                        <div class="silvercart-widget-content_frame">
                            <ul class="vlist">
                                <% control Top.StepList %>
                                    <% control step %>
                                        <% if StepIsVisible %>
                                        <li<% if IsCurrentStep %> class="active"<% end_if %>>
                                            <% if IsCurrentStep %>
                                            <strong><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</strong>
                                            <% else_if isStepCompleted %>
                                            <a href="{$Top.Link}GotoStep/{$StepNr}"><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</a>
                                            <% else_if isPreviousStepCompleted %>
                                            <a href="{$Top.Link}GotoStep/{$StepNr}"><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</a>
                                            <% else %>
                                            <span><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</span>
                                            <% end_if %>
                                        </li>
                                        <% end_if %>
                                    <% end_control %>
                                <% end_control %>
                            </ul>
                        </div>

                    </div>
                </div>
            <% end_if %>
        <% end_control %>

    </div>

     <!-- IE Column Clearing -->
    <div id="ie_clearing"> &#160; </div>
</div>
