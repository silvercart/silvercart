<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
            <h2>$Title</h2>

            $Content
            $Form
            $PageComments
            <% include SilvercartShippingAndBillingAddress %>
        <% else %>
            <% include SilvercartMyAccountLoginOrRegister %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
            <% include SilvercartSideBarCart %>
        <% end_if %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>