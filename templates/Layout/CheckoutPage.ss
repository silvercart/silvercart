<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="ShoppingCartPage">
            <% include BreadCrumbs %>

            <h2>$Title</h2>

            $Content
            $InsertCustomHtmlForm

            <% if CustomHtmlFormStepLinkCancel && ShowCustomHtmlFormStepNavigation %>
                <a href="$CustomHtmlFormStepLinkCancel">Abbrechen</a>
            <% end_if %>

            <% if CustomHtmlFormStepLinkPrev && ShowCustomHtmlFormStepNavigation %>
                <% if CustomHtmlFormStepLinkCancel %>&nbsp;|&nbsp;<% end_if %><a href="$CustomHtmlFormStepLinkPrev">Zurück</a>
            <% end_if %>

            <% if CustomHtmlFormStepLinkNext && ShowCustomHtmlFormStepNavigation %>
                <% if CustomHtmlFormStepLinkCancel %>&nbsp;|&nbsp;<% end_if %><a href="$CustomHtmlFormStepLinkNext">Vor</a>
            <% end_if %>
            $PageComments
        </div>
    </div>
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>
<% end_if %>
<div id="col3">
    <div id="col3_content" class="clearfix">

        <div class="sidebarBox">
            <div class="sidebarBox_content">
                <strong>Schritte</strong>

                <ul>
                    <% control StepList %>
                        <% control step %>
                            <% if stepIsVisible %>
                            <li<% if isCurrentStep %> class="active"<% end_if %>>
                                <% if isStepCompleted %>
                                    <% if ShowCustomHtmlFormStepNavigation %>
                                        <a href="{$Top.Link}GotoStep/{$StepNr}">$StepTitle</a>
                                    <% else %>
                                        $StepTitle
                                    <% end_if %>
                                <% else_if isPreviousStepCompleted %>
                                    <% if ShowCustomHtmlFormStepNavigation %>
                                        <a href="{$Top.Link}GotoStep/{$StepNr}">$StepTitle</a>
                                    <% else %>
                                        $StepTitle
                                    <% end_if %>
                                <% else %>
                                    $StepTitle
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
