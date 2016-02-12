<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>

        <% if CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1>$Title</h1>
        </div>
            $Content
            $Form
            $InsertCustomHtmlForm(SilvercartEditProfileForm)
            $PageComments
        <% else %>
            <% include SilvercartMyAccountLoginOrRegister %>
        <% end_if %>
    </div>
<aside class="span3">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
        <% end_if %>
        $InsertWidgetArea(Sidebar)
</aside>
</div>
