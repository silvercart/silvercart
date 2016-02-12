<% with CustomersOrder %>
    <table class="table table-condensed">
        <tbody>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartPage.ORDER_DATE','Order date') %></td>
                <td class="align-left full">$Created.Nice</td>
            </tr>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartOrder.PAYMENTMETHODTITLE','Payment method') %></td>
                <td class="align-left full">$SilvercartPaymentMethod.Name</td>
            </tr>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartOrder.STATUS','Order status') %></td>
                <td class="align-left full">$SilvercartOrderStatus.Title</td>
            </tr>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartOrder.SHIPPINGRATE','Shipping rate') %></td>
                <td class="align-left full">$HandlingCostShipment.Nice</td>
            </tr>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartOrder.ORDER_VALUE','Orderamount') %></td>
                <td class="align-left full">$AmountTotal.Nice</td>
            </tr>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></td>
                <td class="align-left full">$OrderNumber</td>
            </tr>
            <% if Note %>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartOrder.YOUR_REMARK','Your remark') %></td>
                <td class="align-left full">$getFormattedNote</td>
            </tr>
            <% end_if %>
            <% if TrackingCode %>
            <tr>
                <td class="align-left nowrap"><strong><% _t('SilvercartOrder.Tracking') %></strong></td>
                <td class="align-left full"><a href="{$TrackingLink}" target="blank" title="<% _t('SilvercartOrder.TrackingLinkLabel') %>"><% _t('SilvercartOrder.TrackingLinkLabel') %></a></td>
            </tr>
            <% end_if %>
            <tr>
                <td class="align-left nowrap"><% _t('SilvercartRevocationFormPage.TITLE','Revocation') %></td>
                <td class="align-left full"><a class="silvercart-button btn" href="$CurrentPage.PageByIdentifierCodeLink(SilvercartRevocationFormPage)?o={$ID}"><% _t('SilvercartRevocationForm.GoTo','Go to revocation form') %> <i class="icon icon-caret-right"></i></a></td>
            </tr>

            $OrderDetailInformation
        </tbody>
    </table>
    <div class="row-fluid silvercart-address-equalize">
        <div class="span6">
            <div class="well margin-top">
        <% with SilvercartInvoiceAddress %>
            <% include SilvercartAddressDetailReadOnly %>
        <% end_with %>
            </div>
        </div>
        <div class="span6">
            <div class="well margin-top">
        <% with SilvercartShippingAddress %>
            <% include SilvercartAddressDetailReadOnly %>
        <% end_with %>
            </div>
        </div>
    </div>
    <div class="margin-top">
        {$OrderDetailTable}
    </div>
<% end_with %>
