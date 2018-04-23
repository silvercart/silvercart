<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <style type="text/css">
            body { font-family: Verdana; font-size:70.00%; color:#666; }
            h1 { font-size: 14px; }
            h2 { font-size: 12px; }

            .text-right { text-align: right !important; }
        </style>
    </head>
    <body>
        <h1><%t SilverCart\Model\ShopEmail.ForgotPasswordTITLE 'Reset Password' %></h1>

        <p><%t SilverCart\Model\ShopEmail.HELLO 'Hello' %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>

        <p><%t SilverCart\Model\ShopEmail.ForgotPasswordVISIT_TEXT 'Please visit the following link to reset your password:' %><br/>
            $PasswordResetLink<br/>
            <%t SilverCart\Model\ShopEmail.ForgotPasswordPASSWORT_RESET_LINK_HINT 'If you are not able to click this link, please copy the link to your clipboard and paste it into the address bar of your web browser.' %>
        </p>
        
        <p><%t SilverCart\Model\ShopEmail.ForgotPasswordNO_CHANGE 'If you do not want to change your password, you may ignore this email.' %></p>

        <p><%t SilverCart\Model\ShopEmail.REGARDS 'Best regards' %>,</p>
        <p><%t SilverCart\Model\ShopEmail.YOUR_TEAM 'Your SilverCart ecommerce team' %></p>
    </body>
</html>
