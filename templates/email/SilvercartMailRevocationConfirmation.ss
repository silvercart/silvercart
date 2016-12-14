<style type="text/css">
    h1 { font-size: 14px; }
    h2 { font-size: 12px; }
    body { font-size: 10px; }
</style>
<h1><% _t('SilvercartMailRevocationConfirmation.Title') %></h1>
<p><% _t('SilvercartMailRevocationConfirmation.Text') %></p>
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
<% _t('SilvercartRevocationForm.NameOfConsumer') %>: <strong>{$SalutationText} {$AcademicTitle} {$FirstName} {$Surname}</strong><br/>
<% _t('SilvercartRevocationForm.AddressOfConsumer') %>:<br/>
{$Street} {$StreetNumber}<br/>
{$Addition}<br/>
{$Postcode} {$City}<br/>
{$Country.Title}<br/>
<br/>
<% _t('SilvercartRevocationForm.RevocationOrderData') %>:<br/>
<strong><i>{$RevocationOrderData}</i></strong><br/>
</p>
<br/><br/>
<p><% _t('SilvercartShopEmail.REGARDS', 'Best regards') %>,</p>
<p><% _t('SilvercartShopEmail.YOUR_TEAM', 'Your SilverCart ecommerce team') %></p>
