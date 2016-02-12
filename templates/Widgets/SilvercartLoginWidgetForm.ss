<form class="form full" $FormAttributes>
    $CustomHtmlFormMetadata
    
    <% if HasCustomHtmlFormErrorMessages %>
        <div class="silvercart-error-list">
            <div class="silvercart-error-list_content">
                $CustomHtmlFormErrorMessages
            </div>
        </div>
    <% end_if %>

    <div class="widget-login-fields">
        $CustomHtmlFormFieldByName(emailaddress, CustomHtmlFormFieldEmailPrepend)
        $CustomHtmlFormFieldByName(password, CustomHtmlFormFieldPasswordPrepend)
    </div>
    <div class="clearfix">
    <% loop Actions %>
        <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" value="{$Title}" name="{$Name}" type="submit">{$Title}</button>
    <% end_loop %>
    </div>
    <a class="btn btn-link" href="{$BaseHref}Security/lostpassword"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
</form>
