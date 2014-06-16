<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata

    <fieldset>
        <legend><% _t('SilvercartCheckoutFormStep1.LOGIN') %></legend>

        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(Email)
        $CustomHtmlFormFieldByName(Password)
    </fieldset>

    $CustomHtmlFormSpecialFields

    <div class="actionRow">
        <div class="type-button">
            <a class="forgot-password-plain" href="{$BaseHref}Security/lostpassword"><% _t('Member.BUTTONLOSTPASSWORD') %></a><br/>
            <br/>
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
    </div>
</form>