<% control CustomersOrder %>
<h3>Bestelldetails</h3>
    <table>
        <tr>
            <td><% _t('SilvercartPage.ORDER_DATE','Order date') %></td><td>$Created.Nice</td>
        </tr>
        <tr>
            <td><% _t('SilvercartOrder.SHIPPINGRATE','Shipping rate') %></td><td>$HandlingCostShipment.Nice</td>
        </tr>
        <tr>
            <td><% _t('SilvercartOrder.ORDER_VALUE','Orderamount') %></td><td>$AmountTotal.Nice</td>
        </tr>
        <tr>
            <td><% _t('SilvercartOrder.ORDERNUMBER','Ordernumber') %></td><td>$OrderNumber</td>
        </tr>
        <tr>
            <td><% _t('SilvercartOrder.STATUS','Order status') %></td><td>$SilvercartOrderStatus.Title</td>
        </tr>
        <% if Note %>
        <tr>
            <td><% _t('SilvercartOrder.YOUR_REMARK','Your remark') %></td><td>$Note</td>
        </tr>
        <% end_if %>
    </table>
<div class="subcolumns">
    <div class="c50l">
        <h3><% _t('SilvercartOrderShippingAddress.SINGULARNAME','Order shipping address') %></h3>
        <% control SilvercartShippingAddress %>
            <% include SilvercartAddressTable %>
        <% end_control %>
    </div>
    <div class="c50r">
        <h3><% _t('SilvercartOrderInvoiceAddress.SINGULARNAME','Order invoice address') %></h3>
        <% control SilvercartInvoiceAddress %>
            <% include SilvercartAddressTable %>
        <% end_control %>
    </div>
</div>
<h3><% _t('SilvercartOrderPosition.PLURALNAME','Order positions') %></h3>
    <table>
        <tr>
            <th><% _t('SilvercartPage.PRODUCTNAME','Product name') %></th>
            <th><% _t('SilvercartProduct.DESCRIPTION','Product description') %></th>
            <th><% _t('SilvercartProduct.PRICE_SINGLE','Price single') %></th>
            <th><% _t('SilvercartProduct.QUANTITY_SHORT','Qty.') %></th>
            <th><% _t('SilvercartPage.SUM','Sum') %></th>
        </tr>
        <% control SilvercartOrderPositions %>
        <tr>
            <td>$Title</td>
            <td>$ProductDescription.LimitWordCount(12)</td>
            <td>$Price.Nice</td>
            <td>$Quantity</td>
            <td>$PriceTotal.Nice</td>
        </tr>
        <% end_control %>
    </table>
<% end_control %>
