<h1>{$Subject}</h1>
<% if $CustomContent('HeaderInformationText') %>
    {$CustomContent('HeaderInformationText')}
<% else %>
    <p><%t SilverCart\Model\Pages\NewsletterPage.EmailConfirmationLinkInfo 'Click on the activation link or copy the link to your browser please.' %></p>
<% end_if %>
<p><a href="{$ConfirmationLink}"><%t SilverCart\Model\Pages\NewsletterPage.EmailConfirmationConfirm 'Confirm newsletter registration' %></a></p>
<% if $CustomContent('IgnoreInformationText') %>
    {$CustomContent('IgnoreInformationText')}
<% else %>
    <p><%t SilverCart\Model\Pages\NewsletterPage.EmailConfirmationIgnore 'If you haven\'t requested the newsletter registration just ignore this email.' %></p>
<% end_if %>
<% if $CustomContent('FooterInformationText') %>
    {$CustomContent('FooterInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
    <p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_if %>
