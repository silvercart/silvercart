<div class="form-vertical grouped">
    <h4><% _t('SilvercartMyAccountHolder.ALREADY_HAVE_AN_ACCOUNT') %></h4>
    <form class="form" $FormAttributes >
        $CustomHtmlFormMetadata
        <div class="text-left padding">
            $CustomHtmlFormErrorMessages
        </div>
        
        <div class="row-fluid">
            <div class="span6">
                $CustomHtmlFormFieldByName(Email)
            </div>
            <div class="span6">
                $CustomHtmlFormFieldByName(Password)
            </div>
        </div>
        
        $CustomHtmlFormSpecialFields

        <div class="row-fluid">
            <div class="span6">
                <% loop Actions %>
                    <button class="btn btn-small btn-primary" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
                <% end_loop %>
            </div>
            <div class="span6">
                <a href="{$BaseHref}Security/lostpassword" class="btn btn-small btn-link forgot-password-plain"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
     
            </div>
        </div>
    </form>
</div>