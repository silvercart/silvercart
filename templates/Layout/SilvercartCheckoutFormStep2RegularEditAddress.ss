<div id="col1">
    <div id="col1_content" class="clearfix">
        <h2>$Title</h2>
        $Content
        $Process
        $InsertCustomHtmlForm(SilvercartEditAddressForm)
<% with CurrentFormInstance %>
    <% if ShowCustomHtmlFormStepNavigation %>
        <% if Top.CustomHtmlFormStepLinkCancel %>
        <div class="silvercart-button">
            <div class="silvercart-button_content">
                <a  href="$Top.CustomHtmlFormStepLinkCancel"><% _t('SilvercartPage.CANCEL') %></a>
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

        <div class="widget">
            <div class="widget_content">
                <% with CurrentFormInstance %>
                    <% if ShowCustomHtmlFormStepNavigation %>
                            <strong><% _t('SilvercartPage.STEPS','steps') %></strong>
                            <ul>
                        <% with Top.StepList %>
                            <% with step %>
                                <% if StepIsVisible %>
                                <li<% if IsCurrentStep %> class="active"<% end_if %>>
                                    <% if IsCurrentStep %>
                                    <p><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</p>
                                    <% else_if isStepCompleted %>
                                    <a href="{$Top.Link}GotoStep/{$StepNr}"><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</a>
                                    <% else_if isPreviousStepCompleted %>
                                    <a href="{$Top.Link}GotoStep/{$StepNr}"><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</a>
                                    <% else %>
                                    <p><% if StepImage %>$StepImage<% end_if %>$StepTitle.HTML</p>
                                    <% end_if %>
                                </li>
                                <% end_if %>
                            <% end_with %>
                        <% end_with %>
                            </ul>
                    <% end_if %>
                <% end_with %>
            </div>
        </div>

    </div>

     <!-- IE Column Clearing -->
    <div id="ie_clearing"> &#160; </div>
</div>
