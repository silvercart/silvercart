<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        $Content
        $Form
        $PageComments

<% if PaymentMethods %>
    <% control PaymentMethods %>
        <% if isActive %>
            <div class="clearfix">
            <h2>$Name</h2>
            <% if showPaymentLogos %>
                <% if PaymentLogos %>
                    <span class="float_right">
                        <% control PaymentLogos %>
                            $Image
                        <% end_control %>
                    </span>
                <% end_if %>
            <% end_if %>
            <% if paymentDescription %>
                <p>$paymentDescription</p>
            <% end_if %>
            </div>
        <% end_if %>
    <% end_control %>
<% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
