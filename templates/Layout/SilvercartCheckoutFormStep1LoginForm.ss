<div class="form-horizontal grouped center">
    <h4><% _t('SilvercartMyAccountHolder.ALREADY_HAVE_AN_ACCOUNT') %></h4>
    <form class="form" $FormAttributes >
        $CustomHtmlFormMetadata

        <div class="left padding">
            $CustomHtmlFormErrorMessages
        </div>

        $CustomHtmlFormFieldByName(Email)
        $CustomHtmlFormFieldByName(Password)


        $CustomHtmlFormSpecialFields

        <% loop Actions %>
            <button class="btn btn-small btn-primary" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
        <% end_loop %>
        <br/><br/>
        <a href="{$BaseHref}Security/lostpassword" class="btn btn-small btn-link forgot-password-plain"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
    </form>
</div>