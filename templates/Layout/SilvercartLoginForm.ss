<form class="yform full" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
                $CustomHtmlFormFieldByName(emailaddress)
            </div>
        </div>

        <div class="c50r">
            <div class="subcr">
                $CustomHtmlFormFieldByName(password)
            </div>
        </div>
    </div>

    $CustomHtmlFormSpecialFields

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
    
            <a class="forgot-password-plain" href="{$BaseHref}Security/lostpassword"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
        </div>
    </div>

</form>
