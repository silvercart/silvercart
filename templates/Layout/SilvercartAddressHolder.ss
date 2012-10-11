
<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

        <% if ErrorMessage %>
            <p class="silvercart-message highlighted error32">$ErrorMessage</p>
        <% end_if %>
        <% if SuccessMessage %>
            <p class="silvercart-message highlighted info32">$SuccessMessage</p>
        <% end_if %>
        
        <% if CurrentRegisteredCustomer %>
            <h2><% _t('SilvercartAddressHolder.CURRENT_DEFAULT_ADDRESSES','Your default invoice and shipping addresses') %></h2>
            $Content
            <% control CurrentRegisteredCustomer %>
                <% if hasOnlyOneStandardAddress %>
                    <% control SilvercartInvoiceAddress %>
                        <% include SilvercartAddressDetailReadOnly %>
                    <% end_control %>
                <% else %>
                    <div class="subcolumns silvercart-address-equalize">
                        <div class="c50l">
                            <div class="subcl">
                                <% control SilvercartInvoiceAddress %>
                                    <% include SilvercartAddressDetailReadOnly %>
                                <% end_control %>
                            </div>
                        </div>
                        <div class="c50r">
                            <div class="subcr">
                                <% control SilvercartShippingAddress %>
                                    <% include SilvercartAddressDetailReadOnly %>
                                <% end_control %>
                            </div>
                        </div>
                    </div>
                <% end_if %>
                <div class="silvercart-address-equalize">
                    <% include SilvercartAddressDetail %>
                </div>
            <% end_control %>
            <hr />
            <div class="hidden-form" id="silvercart-add-address-form">
                $insertCustomHtmlForm(SilvercartAddAddressForm)
            </div>
            <div class="silvercart-button">
                <div class="silvercart-button_content">
                    <a href="{$Link}addNewAddress" id="silvercart-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
                </div>
            </div>
            <% require javascript(silvercart/script/SilvercartAddressHolder.js) %>
            
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