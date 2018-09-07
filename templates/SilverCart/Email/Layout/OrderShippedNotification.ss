<h1><%t SilverCart\Model\ShopEmail.ORDER_SHIPPED_NOTIFICATION 'Shipment notification' %></h1>

<p><%t SilverCart\Model\ShopEmail.HELLO 'Hello' %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>
<p><%t SilverCart\Model\ShopEmail.ORDER_SHIPPED_MESSAGE 'Your order has been shipped.' %></p>

<% with $Order %>
    <table>
        <colgroup>
            <col width="40%"></col>
            <col width="30%"></col>
            <col width="30%"></col>
        </colgroup>
        <tr>
            <td valign="top">
                <h2><%t SilverCart\Model\Pages\OrderHolder.OrderDetails 'Order details' %>:</h2>

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
                    <tr>
                        <td>{$fieldLabel('PaymentStatus')}</td>
                        <td>{$PaymentStatus.Title}</td>
                    </tr>
                <% if $Note %>
                    <tr>
                        <td>{$fieldLabel('YourNote')}</td>
                        <td>{$FormattedNote}</td>
                    </tr>
                <% end_if %>
                <% if $TrackingCode %>
                    <tr>
                        <td>{$fieldLabel('TrackingCode')}</td>
                        <td>{$TrackingCode}</td>
                    </tr>
                    <% if $TrackingLink %>
                    <tr>
                        <td>&nbsp;</td>
                        <td><a href="{$TrackingLink}" target="blank" title="{$fieldLabel('TrackingLinkLabel')}">{$fieldLabel('TrackingLinkLabel')}</a></td>
                    </tr>
                    <% end_if %>
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
