<style type="text/css">
    h1 { font-size: 14px; }
    h2 { font-size: 12px; }
    body { font-size: 10px; }
</style>
<p><% _t('SilvercartMailRevocationNotification.Text') %></p>
<p>
<% _t('Silvercart.To') %>:<br/>
<i>
    <strong>{$ShopName}</strong><br/>
    {$ShopStreet} {$ShopStreetNumber}<br/>
    {$ShopPostcode} {$ShopCity}<br/>
    {$ShopCountry.Title}<br/>
</i><br/>
<% _t('SilvercartRevocationForm.RevocationDate') %>: <strong>{$CurrentDate}</strong><br/>
<% _t('SilvercartRevocationForm.OrderDate') %>: <strong>{$OrderDate}</strong><br/>
<% _t('SilvercartRevocationForm.OrderNumber') %>: <strong>{$OrderNumber}</strong><br/>
<% _t('SilvercartRevocationForm.NameOfConsumer') %>: <strong>{$Salutation} {$FirstName} {$Surname}</strong><br/>
<% _t('SilvercartRevocationForm.AddressOfConsumer') %>:<br/>
{$Street} {$StreetNumber}<br/>
{$Addition}<br/>
{$Postcode} {$City}<br/>
{$Country.Title}<br/>
<br/>
<% _t('SilvercartRevocationForm.RevocationOrderData') %>:<br/>
<strong><i>{$RevocationOrderData}</i></strong><br/>
</p>
