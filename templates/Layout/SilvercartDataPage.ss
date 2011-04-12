<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
            <h1>$Title</h1>
            $Content
            $Form
            $InsertCustomHtmlForm(SilvercartEditProfileForm)
            $PageComments
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
