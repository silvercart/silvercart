<h1><%t SilverCart\Model\ShopEmail.ForgotPasswordTITLE 'Reset Password' %></h1>

<p><%t SilverCart\Model\ShopEmail.HELLO 'Hello' %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>

<p><%t SilverCart\Model\ShopEmail.ForgotPasswordVISIT_TEXT 'Please visit the following link to reset your password:' %><br/>
    <a href="{$PasswordResetLink}"><%t SilverCart\Model\ShopEmail.ForgotPasswordTITLE 'Reset Password' %></a></p>

<p><%t SilverCart\Model\ShopEmail.ForgotPasswordPASSWORT_RESET_LINK_HINT 'If you are not able to click this link, please copy the link to your clipboard and paste it into the address bar of your web browser.' %><br/>
    <pre style="margin-left: 6px;">{$PasswordResetLink}</pre>
</p>

<p><%t SilverCart\Model\ShopEmail.ForgotPasswordNO_CHANGE 'If you do not want to change your password, you may ignore this email.' %></p>

<p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
<p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>