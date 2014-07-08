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
            <% loop Actions %>
                $Field
            <% end_loop %>
            <a class="forgot-password-plain" href="{$CurrentPage.LostPasswordLink}"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
        </div>
    </div>
</form>