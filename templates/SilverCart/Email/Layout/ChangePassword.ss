<h1>{$Subject}</h1>
<p><%t SilverCart\Model\ShopEmail.HELLO 'Hello' %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>
<% if $CustomContent('HeaderInformationText') %>
    {$CustomContent('HeaderInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.ForgotPasswordVISIT_TEXT 'Please visit the following link to reset your password:' %><br/>
<% end_if %>
<a href="{$PasswordResetLink}"><%t SilverCart\Model\ShopEmail.ForgotPasswordTITLE 'Reset Password' %></a></p>
<% if $CustomContent('ButtonInformationText') %>
    {$CustomContent('ButtonInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.ForgotPasswordPASSWORT_RESET_LINK_HINT 'If you are not able to click this link, please copy the link to your clipboard and paste it into the address bar of your web browser.' %></p>
<% end_if %>
<p><pre style="margin-left: 6px;">{$PasswordResetLink}</pre></p>
<% if $CustomContent('SecurityInformationText') %>
    {$CustomContent('SecurityInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.ForgotPasswordNO_CHANGE 'If you do not want to change your password, you may ignore this email.' %></p>
<% end_if %>
<% if $CustomContent('FooterInformationText') %>
    {$CustomContent('FooterInformationText')}
<% else %>
    <p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
    <p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
<% end_if %>