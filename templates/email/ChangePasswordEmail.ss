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
        <h1><% _t('SilvercartMailForgotPassword.TITLE') %></h1>

        <p><% _t('SilvercartShopEmail.HELLO') %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>

        <p><% _t('SilvercartMailForgotPassword.VISIT_TEXT') %><br/>
            $PasswordResetLink<br/>
            <% _t('SilvercartMailForgotPassword.PASSWORT_RESET_LINK_HINT') %>
        </p>
        
        <p><% _t('SilvercartMailForgotPassword.NO_CHANGE') %></p>        

        <p><% _t('SilvercartShopEmail.REGARDS') %>,</p>
        <p><% _t('SilvercartShopEmail.YOUR_TEAM') %></p>
    </body>
</html>
