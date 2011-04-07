<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <h2>$Title</h2>

        <% if isRegisteredCustomer %>
            $Content
        <% else %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $SubNavigation
        <% include SilvercartSideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>