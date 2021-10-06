<h1>{$Subject}</h1>
<% if $CustomContent('HeaderInformationText') %>
    {$CustomContent('HeaderInformationText')}
<% else %>
    <p><%t SilverCart\Model\Pages\NewsletterPage.EmailConfirmationSuccessMessage 'Your newsletter registration was successful!' %></p>
    <p><%t SilverCart\Model\Pages\NewsletterPage.EmailConfirmationHaveFun 'Hopefully our offers will be of good use to you.' %></p>
<% end_if %>
<% if $CustomContent('FooterInformationText') %>
    {$CustomContent('FooterInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
    <p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_if %>