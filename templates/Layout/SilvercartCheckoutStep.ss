<div id="col1">
    <div id="col1_content" class="clearfix">
        <h2>$CurrentFormInstance.StepTitle.HTML</h2>
        $Content
        $Process
        $insertCustomHtmlForm
        
        <% with CurrentFormInstance %>
            <% if ShowCustomHtmlFormStepNavigation %>
                <% if Top.CustomHtmlFormStepLinkCancel %>
                    <div class="silvercart-button left">
                        <div class="silvercart-button_content">
                            <a href="$Top.CustomHtmlFormStepLinkCancel"><% _t('SilvercartPage.CANCEL') %></a>
                        </div>
                    </div>
                <% end_if %>
            <% end_if %>
        <% end_with %>

        $PageComments
    </div>
</div>

<div id="col3">
    <div id="col3_content" class="clearfix">

        <% with CurrentFormInstance %>
            <% if ShowCustomHtmlFormStepNavigation %>
                <div class="silvercart-widget">
                    <div class="silvercart-widget_content">
                
                        <h2><% _t('SilvercartPage.STEPS','steps') %></h2>
                        <div class="silvercart-widget-content_frame">
                            <ul class="vlist">
                                <% loop Top.StepList %>
                                    <% with step %>
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
                                    <% end_with %>
                                <% end_loop %>
                            </ul>
                        </div>

                    </div>
                </div>
            <% end_if %>
        <% end_with %>

    </div>

     <!-- IE Column Clearing -->
    <div id="ie_clearing"> &#160; </div>
</div>
