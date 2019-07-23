<% with $Customer %>
<h1><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInConfirmationTitle 'Email verification completed' %></h1>
<p><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInConfirmationInfo 'Congratulations and welcome to our shop! Your email address was successfully confirmed and you are now able to use your customer account in our shop.' %></p>
<a style="display: inline-block; padding: 8px 12px 8px 12px;margin: 22px 0px 22px 0px;background-color: #94c11c;color: #ffffff;font-weight: bold;" href="{$PageByIdentifierCode('SilvercartFrontPage').Link}"><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInConfirmationVisit 'Visit our shop' %> &raquo;</a><br/>
<hr/>
<h1><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInConfirmationCustomerData 'Your customer data' %></h1>
<p><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInConfirmationCustomerDataInfo 'Below you can find your customer data.' %></p>
<table>
    <tr><td>{$fieldLabel('CustomerNumber')}:</td><td style="font-weight: bold;">{$CustomerNumber}</td></tr>
    <tr><td>{$fieldLabel('Salutation')}:</td><td><% if $Salutation %>{$Salutation}<% else %>---<% end_if %></td></tr>
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
<hr/>
<a style="display: inline-block; padding: 8px 12px 8px 12px;margin: 22px 0px 22px 0px;background-color: #94c11c;color: #ffffff;font-weight: bold;" href="{$PageByIdentifierCode('SilvercartMyAccountHolder').Link}"><%t SilverCart\Model\Pages\RegistrationPage.OptInGoToMyAccount 'Go to my account' %> &raquo;</a><br/>
<br/>
<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_with %>