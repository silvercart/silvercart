<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
    <% if $CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        {$EditProfileForm}
    <% else %>
        <% include SilverCart/Model/Pages/MyAccountLoginOrRegister %>
    <% end_if %>
    </div>
<aside class="span3">
    <% if $CurrentRegisteredCustomer %>
        {$SubNavigation}
    <% end_if %>
    $InsertWidgetArea(Sidebar)
</aside>
</div>
