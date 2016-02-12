<form class="page form form-horizontal" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages


    $CustomHtmlFormFieldByName(emailaddress)
    $CustomHtmlFormFieldByName(password)


    $CustomHtmlFormSpecialFields
    
    <% loop Actions %>
    <button title="{$Title}" value="{$Title}" name="{$Name}" id="{$Id}" class="btn btn-small btn-primary" type="submit">{$Title}</button>
    <% end_loop %>
    <br/><br/>
    <a href="{$BaseHref}Security/lostpassword" class="btn btn-small btn-link forgot-password-plain"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
</form>
