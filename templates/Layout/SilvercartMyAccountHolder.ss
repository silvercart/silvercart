<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
            <h1>$Title</h1>
        
            $Content
            
            <% loop Children %>
                <% if hasSummary %>
            <div class="silvercart-section">
                <div class="silvercart-section_content clearfix">
                    <h3>{$SummaryTitle}:</h3>
                    {$Summary}
                    <div class="silvercart-button right">
                        <div class="silvercart-button_content">
                            <a href="{$Link}"><% _t('Silvercart.MORE') %></a>
                        </div>
                    </div>
                </div>
            </div>
                <% end_if %>
            <% end_loop %>
            
        <% else %>
            <% include SilvercartMyAccountLoginOrRegister %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
        <% end_if %>
        
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
