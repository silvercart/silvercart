<p><%t SilverCart\Model\ShopEmail.RevocationNotificationText 'A customer performed a revocation with the following data:' %></p>
<p>
<%t SilverCart\Dev\Tools.To 'To' %>:<br/>
<i>
    <strong>{$ShopName}</strong><br/>
    {$ShopStreet} {$ShopStreetNumber}<br/>
    {$ShopPostcode} {$ShopCity}<br/>
    {$ShopCountry.Title}<br/>
</i><br/>
<%t SilverCart\Forms\RevocationForm.RevocationDate 'Date of revocation' %>: <strong>{$CurrentDate}</strong><br/>
<%t SilverCart\Forms\RevocationForm.OrderDate 'Order date / Delivery date' %>: <strong>{$OrderDate}</strong><br/>
<%t SilverCart\Forms\RevocationForm.OrderNumber 'Order number' %>: <strong>{$OrderNumber}</strong><br/>
<%t SilverCart\Forms\RevocationForm.NameOfConsumer 'Name of the customer' %>: <strong>{$SalutationText} {$AcademicTitle} {$FirstName} {$Surname}</strong><br/>
<%t SilverCart\Forms\RevocationForm.AddressOfConsumer 'Address of the customer' %>:<br/>
<strong style="margin-left: 6px; display: inline-block;"><i>
{$Street} {$StreetNumber}<br/>
{$Addition}<br/>
{$Postcode} {$City}<br/>
{$Country.Title}<br/>
</i></strong><br/>
<br/>
<%t SilverCart\Forms\RevocationForm.RevocationOrderData 'I/We hereby revoke the concluded contract for buying the following goods / the performance of the following services' %>:<br/>
<strong><i>{$RevocationOrderData}</i></strong><br/>
</p>
