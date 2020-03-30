<h1><%t SilverCart\Model\Pages\ContactFormPage.REQUEST 'Request via contact form' %></h1>

<p><%t SilverCart\Model\Pages\ContactFormPage.EmailContactRequest 'A customer sent a contact request.' %></p>

<h2><%t SilverCart\Model\Order\Order.CUSTOMERDATA 'Customer Data' %></h2>
<% with ContactMessage %>
<table>
    <tr><td>{$fieldLabel('Name')}:</td><td><strong>{$SalutationText} {$FirstName} {$Surname}</strong></td></tr>
    <tr><td>{$fieldLabel('Email')}:</td><td><strong>{$Email}</strong></td></tr>
<% if $Street %>
    <tr><td>{$fieldLabel('Street')}:</td><td><strong>{$Street} {$StreetNumber}</strong></td></tr>
<% end_if %>
<% if $Postcode %>
    <tr><td>{$fieldLabel('Postcode')}:</td><td><strong>{$Postcode}</strong></td></tr>
<% end_if %>
<% if $City %>
    <tr><td>{$fieldLabel('City')}:</td><td><strong>{$City}</strong></td></tr>
<% end_if %>
<% if $Country %>
    <tr><td>{$fieldLabel('Country')}:</td><td><strong>{$Country.Title}</strong></td></tr>
<% end_if %>
<% if $Phone %>
    <tr><td>{$fieldLabel('Phone')}:</td><td><strong>{$Phone}</strong></td></tr>
<% end_if %>
</table>

<h2><%t SilverCart\Model\ShopEmail.EMAILTEXT 'Message' %></h2>
<p>{$Message.RAW}</p>
<% end_with %>