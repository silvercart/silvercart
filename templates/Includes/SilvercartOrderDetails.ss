<% control CustomersOrder %>
    <h3><% _t('SilvercartOrderDetailPage.TITLE') %></h3>
        <table>
            <colgroup>
                <col width="25%"></col>
                <col width="75%"></col>
            </colgroup>
            <tbody>
                <tr>
                    <td><% _t('SilvercartPage.ORDER_DATE','Order date') %></td>
                    <td>$Created.Nice</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.PAYMENTMETHODTITLE','Payment method') %></td>
                    <td>$SilvercartPaymentMethod.Name</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.STATUS','Order status') %></td>
                    <td>$SilvercartOrderStatus.Title</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.SHIPPINGRATE','Shipping rate') %></td>
                    <td>$HandlingCostShipment.Nice</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.ORDER_VALUE','Orderamount') %></td>
                    <td>$AmountTotal.Nice</td>
                </tr>
                <tr>
                    <td><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></td>
                    <td>$OrderNumber</td>
                </tr>
                <% if Note %>
                <tr>
                    <td><% _t('SilvercartOrder.YOUR_REMARK','Your remark') %></td>
                    <td>$getFormattedNote</td>
                </tr>
                <% end_if %>
                <% if TrackingCode %>
                <tr>
                    <td><strong><% _t('SilvercartOrder.Tracking') %></strong></td>
                    <td><a href="{$TrackingLink}" target="blank" title="<% _t('SilvercartOrder.TrackingLinkLabel') %>"><% _t('SilvercartOrder.TrackingLinkLabel') %></a></td>
                </tr>
                <% end_if %>

                $OrderDetailInformation
            </tbody>
        </table>
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
    <h3><% _t('SilvercartOrderPosition.PLURALNAME','Order positions') %></h3>
    $OrderDetailTable
<% end_control %>
