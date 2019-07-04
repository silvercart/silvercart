<% with $Customer %>
<h1><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInNotificationTitle 'New customer registration' %></h1>
<h2><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInNotificationCustomerData 'Customer data' %></h2>
<table>
    <tr><td>{$fieldLabel('CustomerNumber')}:</td><td style="font-weight: bold;">{$CustomerNumber}</td></tr>
    <tr><td>{$fieldLabel('ShopID')}:</td><td style="font-weight: bold;">{$ID}</td></tr>
    <tr><td>{$fieldLabel('Salutation')}:</td><td><% if $Salutation %>{$Salutation}<% else %>---<% end_if %></td></tr>
    <tr><td>{$fieldLabel('AcademicTitle')}:</td><td><% if $AcademicTitle %>{$AcademicTitle}<% else %>---<% end_if %></td></tr>
    <tr><td>{$fieldLabel('FirstName')}:</td><td><% if $FirstName %>{$FirstName}<% else %>---<% end_if %></td></tr>
    <tr><td>{$fieldLabel('Surname')}:</td><td><% if $Surname %>{$Surname}<% else %>---<% end_if %></td></tr>
    <tr><td>{$fieldLabel('EmailAddress')}:</td><td><% if $Email %>{$Email}<% else %>---<% end_if %></td></tr>
</table>
<div style="display: inline-block; vertical-align: top;">
    <div style="display: inline-block; text-align: left; padding: 32px 16px 0px 0px; min-width: 200px;">
        <span style="color:#929392;">{$fieldLabel('InvoiceAddress')}:</span><br>
        {$InvoiceAddress.forEmail}
    </div>
    <div style="display: inline-block; text-align: left; padding: 32px 16px 0px 0px;">
        <span style="color:#929392;">{$fieldLabel('ShippingAddress')}:</span><br>
        {$ShippingAddress.forEmail}
    </div>
</div>
<% end_with %>