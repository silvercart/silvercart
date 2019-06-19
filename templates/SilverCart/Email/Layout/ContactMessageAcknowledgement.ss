<h1><%t SilverCart\Model\Pages\ContactFormPage.WeReceivedYourRequest 'We received your request.' %></h1>

<p><%t SilverCart\Model\Pages\ContactFormPage.ThankYouForYourRequest 'Thank you for your request. We received your message and will respond as soon as possible.' %></p>

<h2><%t SilverCart\Model\Pages\ContactFormPage.YourData 'Your data:' %></h2>
<% with $ContactMessage %>
<table>
    <tr><td>{$fieldLabel('Name')}:</td><td><strong>{$Salutation} {$FirstName} {$Surname}</strong></td></tr>
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