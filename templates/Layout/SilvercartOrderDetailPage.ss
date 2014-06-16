<div id="col1">
    <div id="col1_content" class="clearfix">
        <div id="silvercart-breadcrumbs-id" class="silvercart-breadcrumbs clearfix">
            <p>$getBreadcrumbs</p>
        </div>

        <% if CurrentRegisteredCustomer %>
            <h1>$Title</h1>

            $Content
            $SearchResults
            $Form
            $PageComments
            <% include SilvercartOrderDetails %>
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
