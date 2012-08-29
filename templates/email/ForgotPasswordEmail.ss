<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <style type="text/css">
            body { font-family: Verdana; font-size:70.00%; color:#666; }
            h1 { font-size: 14px; }
            h2 { font-size: 12px; }

            .right { text-align: right; }
        </style>
    </head>
    <body>        
        <h1><% _t('SilvercartMailForgotPassword.TITLE') %></h1>

        <p><% _t('SilvercartShopEmail.HELLO', 'Hello') %> $Salutation $FirstName $Surname,</p>

        <p><% sprintf(_t('SilvercartMailForgotPassword.VISIT_TEXT'),$PasswordResetLink) %></p>
        
        <p><% _t('SilvercartMailForgotPassword.NO_CHANGE') %></p>        

        <p><% _t('SilvercartShopEmail.REGARDS', 'Best regards') %>,</p>
        <p><% _t('SilvercartShopEmail.YOUR_TEAM', 'Your SilverCart ecommerce team') %></p>
    </body>
</html>