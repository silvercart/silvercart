<% with $Customer %>
<h1><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInTitle 'Confirm your email address' %></h1>
<p><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInLinkInfo 'Please click on the activation link or copy the link to your browser.' %></p>
<a style="display: inline-block; padding: 8px 12px 8px 12px;margin: 22px 0px 22px 0px;background-color: #94c11c;color: #ffffff;font-weight: bold;" href="{$RegistrationOptInConfirmationLink}"><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInConfirmYourEmailAddress 'Confirm your email address' %></a><br/>
<a style="display: inline-block; margin: 0px 0px 22px 0px;color: #919191;font-style: italic;word-break: break-all;" href="{$RegistrationOptInConfirmationLink}">{$RegistrationOptInConfirmationLink}</a>
<p><%t SilverCart\Model\Pages\RegistrationPage.EmailOptInIgnore 'If you haven\'t requested the registration just ignore this email.' %></p>
<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_with %>