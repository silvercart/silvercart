<h1><%t SilverCart\Model\ShopEmail.RevocationConfirmationTitle 'Revocation confirmation' %></h1>
<p><%t SilverCart\Model\ShopEmail.RevocationConfirmationText 'We hereby confirm that we received your revocation with the following data:' %></p>
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
<strong><i>{$RevocationOrderData.RAW}</i></strong>
</p>
<br/>
<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
