<h1>{$Subject}</h1>
<p><%t SilverCart\Model\ShopEmail.HELLO 'Hello' %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>
<% if $CustomContent('HeaderInformationText') %>
    {$CustomContent('HeaderInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.ORDER_ARRIVED 'We confirm your order, many thanks.' %></p>
<% end_if %>
<% with $Order %>
<div style="text-align: center; border: 1px solid #ccc; background-color: #fafafa;">
    <div style="display: inline-block; text-align: left; vertical-align: top;">
        <div style="padding: 12px 16px;">
            <i><%t SilverCart\Model\Pages\OrderHolder.OrderDetails 'Order details' %>:</i><br/>
            <table style="display: inline-block;">
                <tr>
                    <td style="padding: 0.2em 0.5em 0.2em 0;">{$fieldLabel('Email')}</td>
                    <td style="padding: 0.2em 0 0.2em 0.5em;">{$CustomersEmail}</td>
                </tr>
                <tr>
                    <td style="padding: 0.2em 0.5em 0.2em 0;"><%t SilverCart\Model\Pages\Page.ORDER_DATE 'Order date' %></td>
                    <td style="padding: 0.2em 0 0.2em 0.5em;">{$Created.Nice}</td>
                </tr>
            <% if $ExpectedDelivery %>
                <tr>
                   <td style="padding: 0.2em 0.5em 0.2em 0;">{$fieldLabel('ExpectedDelivery')}</td>
                   <td style="padding: 0.2em 0 0.2em 0.5em;"><strong style="color:#28a745">{$ExpectedDeliveryNice}</strong></td>
               </tr>
            <% end_if %>
                <tr>
                    <td style="padding: 0.2em 0.5em 0.2em 0;"><%t SilverCart\Model\Order\NumberRange.ORDERNUMBER 'Ordernumber' %></td>
                    <td style="padding: 0.2em 0 0.2em 0.5em;"><strong>{$OrderNumber}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 0.2em 0.5em 0.2em 0;">{$fieldLabel('OrderStatus')}</td>
                    <td style="padding: 0.2em 0 0.2em 0.5em;">{$OrderStatus.Title}</td>
                </tr>
                <tr>
                    <td style="padding: 0.2em 0.5em 0.2em 0;">{$fieldLabel('PaymentStatus')}</td>
                    <td style="padding: 0.2em 0 0.2em 0.5em;">{$PaymentStatus.Title}</td>
                </tr>
            <% if $Note %>
                <tr>
                    <td style="padding: 0.2em 0.5em 0.2em 0;">{$fieldLabel('YourNote')}</td>
                    <td style="padding: 0.2em 0 0.2em 0.5em;">{$FormattedNote}</td>
                </tr>
            <% end_if %>
            </table>
        </div>
    </div>
    <div style="display: inline-block; vertical-align: top;">
        <div style="display: inline-block; text-align: left; padding: 12px 16px;">
            <i>{$fieldLabel('ShippingAddress')}:</i><br>
            <strong>{$ShippingAddressTable}</strong>
        </div>
        <div style="display: inline-block; text-align: left; padding: 12px 16px;">
            <i>{$fieldLabel('InvoiceAddress')}:</i><br>
            {$InvoiceAddressTable}
        </div>
    </div>
    <% if $Up.CustomContent('AddressInformationText') %>
        {$Up.CustomContent('AddressInformationText')}
    <% end_if %>
</div>
<h2><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'Ordered products' %>:</h2>
    <% if $IsPriceTypeGross %>
        <% include SilverCart\Email\OrderDetailTableGross %>
    <% else %>
        <% include SilverCart\Email\OrderDetailTableNet %>
    <% end_if %>
<% end_with %>
<% if $CustomContent('OrderInformationText') %>
    {$CustomContent('OrderInformationText')}
<% end_if %>
<% if $CustomContent('FooterInformationText') %>
    {$CustomContent('FooterInformationText')}
<% else %><p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
    <p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_if %>