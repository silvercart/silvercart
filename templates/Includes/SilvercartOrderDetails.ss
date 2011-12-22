<% control CustomersOrder %>
    <h3><% _t('SilvercartOrderDetailPage.TITLE') %></h3>
        <table>
            <colgroup>
                <col width="35%"></col>
                <col width="65%"></col>
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
                    <td>$Note</td>
                </tr>
                <% end_if %>

                $OrderDetailInformation
            </tbody>
        </table>
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
    <h3><% _t('SilvercartOrderPosition.PLURALNAME','Order positions') %></h3>
    <table>
        <colgroup>
            <col width="20%"></col>
            <col width="45%"></col>
            <col width="15%"></col>
            <col width="5%"></col>
            <col width="15%"></col>
        </colgroup>
        <thead>
            <tr>
                <th><% _t('SilvercartPage.PRODUCTNAME','Product name') %></th>
                <th><% _t('SilvercartProduct.DESCRIPTION','Product description') %></th>
                <th><% _t('SilvercartProduct.PRICE_SINGLE','Price single') %></th>
                <th><% _t('SilvercartProduct.QUANTITY_SHORT','Qty.') %></th>
                <th><% _t('SilvercartPage.SUM','Sum') %></th>
            </tr>
        </thead>
        <tbody>
            <% control SilvercartOrderPositions %>
                <% include SilvercartOrderDetailsPosition %>
                <% if SilvercartVoucherCode %>
                    <tr class="subrow">
                        <td colspan="5">

                            <% if MoreThanOneProduct %>
                                <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERTITLE') %>
                                <ul>
                                    <% control VoucherCodes %>
                                        <li>"<strong>$code</strong>"</li>
                                    <% end_control %>
                                </ul>
                                <% _t('SilvercartVoucherOrderDetailPage.PLURALVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                                <strong><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_PLURAL') %></strong>
                            <% else %>
                                <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERTITLE') %>
                                "<strong>$SilvercartVoucherCode</strong>".<br />
                                <% _t('SilvercartVoucherOrderDetailPage.SINGULARVOUCHERVALUETITLE') %> {$SilvercartVoucherValue.Nice}.<br />
                                <strong><% _t('SilvercartVoucherOrderDetailPage.WARNING_PAYBEFOREREDEEMING_SINGULAR') %></strong>
                            <% end_if %>
                        </td>
                    </tr>
                <% end_if %>
            <% end_control %>
        </tbody>
    </table>
<% end_control %>
