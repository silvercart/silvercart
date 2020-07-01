<div class="row">
    <div class="span9">
        <div id="silvercart-breadcrumbs-id" class="silvercart-breadcrumbs clearfix">
            <p>$getBreadcrumbs</p>
        </div>

    <% if CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
           <h1>$Title</h1>  
        </div>
        {$Content}
        <% include SilvercartOrderDetails %>
    <% else %>
        <% include SilvercartMyAccountLoginOrRegister %>
    <% end_if %>
    </div><!--end span9-->
    <aside class="span3">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
        <% end_if %>
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>