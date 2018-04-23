<h1><%t SilverCart\Model\ShopEmail.Title_OrderNotification 'Order notification' %></h1>

<% with $Order %>
    <table>
        <colgroup>
            <col width="40%"></col>
            <col width="30%"></col>
            <col width="30%"></col>
        </colgroup>
        <tr>
            <td valign="top">
                <h2><%t SilverCart\Model\Pages\OrderDetailPage.TITLE 'Order details' %>:</h2>

                <table>
                    <tr>
                        <td>{$fieldLabel('Email')}</td>
                        <td>{$CustomersEmail}</td>
                    </tr>
                    <tr>
                        <td><%t SilverCart\Model\Pages\Page.ORDER_DATE 'Order date' %></td>
                        <td>{$Created.Nice}</td>
                    </tr>
                    <tr>
                        <td><%t SilverCart\Model\Order\NumberRange.ORDERNUMBER 'Ordernumber' %></td>
                        <td>{$OrderNumber}</td>
                    </tr>
                    <tr>
                        <td>{$fieldLabel('OrderStatus')}</td>
                        <td>{$OrderStatus.Title}</td>
                    </tr>
                <% if $Note %>
                    <tr>
                        <td>{$fieldLabel('YourNote')}</td>
                        <td>{$FormattedNote}</td>
                    </tr>
                <% end_if %>
                </table>
            </td>
            <td valign="top">
                <h2><%t SilverCart\Model\Pages\Page.SHIPPING_ADDRESS 'Shipping address' %>:</h2>
                {$ShippingAddressTable}
            </td>
            <td valign="top">
                <h2>{$fieldLabel('InvoiceAddress')}:</h2>
                {$InvoiceAddressTable}
            </td>
        </tr>
    </table>

    <h2><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'Ordered products' %>:</h2>
    {$OrderDetailTable}
<% end_with %>

<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
