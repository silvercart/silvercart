<form class="page form form-vertical" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    <div class="row-fluid">
        <div class="span4">
            $CustomHtmlFormFieldByName(emailaddress)
        </div>
        <div class="span4">
             $CustomHtmlFormFieldByName(password)
        </div>
        <div class="span4 last">
        </div>
    </div>    
    
   


    $CustomHtmlFormSpecialFields
    <div class="row-fluid">
        <div class="span4">
              <% loop Actions %>
    <button title="{$Title}" value="{$Title}" name="{$Name}" id="{$Id}" class="btn btn-small btn-primary" type="submit">{$Title}</button>
    <% end_loop %>
        </div>
        <div class="span4">
             <a href="{$BaseHref}Security/lostpassword" class="btn btn-small btn-link forgot-password-plain"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
        </div>
        <div class="span4 last">
        </div>
    </div>    
  
  
</form>
