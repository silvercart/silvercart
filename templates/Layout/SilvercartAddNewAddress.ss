<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>

        <% if ErrorMessage %>
        <div class="alert alert-error">
            <button class="close" data-dismiss="alert" type="button">×</button>
            <p>$ErrorMessage</p>
        </div>
        <% end_if %>
        <% if SuccessMessage %>
        <div class="alert alert-success">
            <button class="close" data-dismiss="alert" type="button">×</button>
            <p>$SuccessMessage</p>
        </div>
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
 <aside class="span3">
        <% if CurrentRegisteredCustomer %>
            $SubNavigation
        <% end_if %>
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>