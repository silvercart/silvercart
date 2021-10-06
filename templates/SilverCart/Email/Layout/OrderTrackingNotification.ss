<h1>{$Subject}</h1>
<p><%t SilverCart\Model\ShopEmail.HELLO 'Hello' %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>
<% if $CustomContent('HeaderInformationText') %>
    {$CustomContent('HeaderInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.OrderTrackingMessage1 'Your order has been shipped.' %></p>
    <p><%t SilverCart\Model\ShopEmail.OrderTrackingMessage2 'With this email you are receiving your shipment tracking information.' %></p>
<% end_if %>
<% with $Order %>
    <% if $TrackingCode %>
<table>
    <tr>
        <td><%t SilverCart\Model\Pages\Page.ORDER_DATE 'Order date' %></td>
        <td>{$Created.Nice}</td>
    </tr>
    <tr>
        <td><%t SilverCart\Model\Order\NumberRange.ORDERNUMBER 'Ordernumber' %></td>
        <td>{$OrderNumber}</td>
    </tr>
    <tr>
        <td>{$fieldLabel('ShippingMethod')}</td>
        <td>{$ShippingMethod.TitleWithCarrier}</td>
    </tr>
    <tr>
        <td>{$fieldLabel('TrackingCode')}</td>
        <td style="font-weight: bold;">{$TrackingCode}</td>
    </tr>
        <% if $TrackingLink %>
    <tr>
        <td>{$fieldLabel('TrackingLink')}</td>
        <td style="word-break: break-all;">{$TrackingLink}</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><a href="{$TrackingLink}" target="blank" title="{$fieldLabel('TrackingLinkLabel')}">{$fieldLabel('TrackingLinkLabel')}</a></td>
    </tr>
            <% if $Up.CustomContent('ButtonInformationText') %>
    <tr>
        <td>&nbsp;</td>
        <td>{$Up.CustomContent('ButtonInformationText')}</td>
    </tr>
            <% end_if %>
        <% end_if %>
</table>
    <% end_if %>

    <h2><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'Ordered products' %>:</h2>
    <% if $IsPriceTypeGross %>
        <% include SilverCart\Email\OrderDetailTableGross %>
    <% else %>
        <% include SilverCart\Email\OrderDetailTableNet %>
    <% end_if %>
<% end_with %>
<% if $CustomContent('FooterInformationText') %>
    {$CustomContent('FooterInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
    <p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_if %>