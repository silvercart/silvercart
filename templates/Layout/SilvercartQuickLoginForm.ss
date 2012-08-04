<form name="QuickLogin" class="yform full" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <div class="subcolumns">
        <div class="c50l">
            $CustomHtmlFormFieldByName(emailaddress)
        </div>
        <div class="c50r">
            $CustomHtmlFormFieldByName(password)
        </div>
    </div>

    <a href="{$BaseHref}Security/lostpassword"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
    
    <div id="silvercart-quicklogin-form-actions">
        <input type="reset" id="silvercart-quicklogin-form-cancel" value="<% _t('SilvercartPage.CANCEL') %>" />
        <% loop Actions %>
            $Field
        <% end_loop %>
    </div>
</form>
