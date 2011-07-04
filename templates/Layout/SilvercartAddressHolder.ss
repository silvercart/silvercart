
<script language="javascript">
    $(document).ready(function(){
        $('#silvercart-add-address-link').click(function(event) {
            toggleAddAddressForm(event);
        });
        $('#silvercart-add-address-form-cancel-id').click(function(event) {
            toggleAddAddressForm(event);
        });
    });
    
    function toggleAddAddressForm(event) {
        event.preventDefault();
        $('#silvercart-add-address-form').slideToggle('slow', function() {
            if ($(this).is(':visible')) {
                $('#silvercart-add-address-link').fadeOut();
            } else {
                $('#silvercart-add-address-link').fadeIn();
            }
        });
    }
</script>

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
    <div class="subcolumns">
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
            <% include SilvercartAddressDetail %>
            <% end_control %>
            <hr />
            <div class="hidden-form" id="silvercart-add-address-form">
                $insertCustomHtmlForm(SilvercartAddAddressForm)
            </div>
            <a href="{$Link}addNewAddress" class="silvercart-icon-with-text-button big add16" id="silvercart-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
            
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