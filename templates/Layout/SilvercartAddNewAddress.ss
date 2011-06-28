<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if ErrorMessage %>
            <p class="silvercart-message highlighted error32">$ErrorMessage</p>
        <% end_if %>
        <% if SuccessMessage %>
            <p class="silvercart-message highlighted info32">$SuccessMessage</p>
        <% end_if %>
        
        <% if CurrentRegisteredCustomer %>
            <h2><% _t('SilvercartAddressHolder.ADD', 'Add new address') %></h2>
            $Content
            <div id="silvercart-add-address-form">
                $insertCustomHtmlForm(SilvercartAddAddressForm)
            </div>
            
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