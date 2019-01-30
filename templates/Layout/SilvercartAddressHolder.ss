<div class="row-fluid">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>

        <% if ErrorMessage %>
            <p class="silvercart-message highlighted error32">$ErrorMessage</p>
        <% end_if %>
        <% if SuccessMessage %>
            <p class="silvercart-message highlighted info32">$SuccessMessage</p>
        <% end_if %>
        
        <% if CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1><% _t('SilvercartAddressHolder.CURRENT_DEFAULT_ADDRESSES','Your default invoice and shipping addresses') %></h1>
        </div>
            $Content
            <% with CurrentRegisteredCustomer %>
                <% if hasOnlyOneStandardAddress %>
                    <% with SilvercartInvoiceAddress %>
                        <% include SilvercartAddressDetailReadOnly %>
                    <% end_with %>
                <% else %>
                    <div class="row-fluid silvercart-address-equalize">
                        <div class="span4">
                                <% with SilvercartInvoiceAddress %>
                                    <% include SilvercartAddressDetailReadOnly %>
                                <% end_with %>
                        </div>
                        <div class="span4">
                                <% with SilvercartShippingAddress %>
                                    <% include SilvercartAddressDetailReadOnly %>
                                <% end_with %>
                        </div>
                    </div>
                <% end_if %>
                <div class="silvercart-address-equalize">
                    <% include SilvercartAddressDetail %>
                </div>
            <% end_with %>
            <% if $CurrentRegisteredCustomer.SilvercartInvoiceAddress.canCreate %>
            <hr />
            <div class="hidden-form" id="silvercart-add-address-form">
                $insertCustomHtmlForm(SilvercartAddAddressForm)
            </div>
            <a class="btn btn-small" href="{$Link}addNewAddress" id="silvercart-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
            <% end_if %>
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