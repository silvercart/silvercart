<form class="yform full" $FormAttributes>
    $CustomHtmlFormMetadata
    
    <% if HasCustomHtmlFormErrorMessages %>
        <div class="silvercart-error-list">
            <div class="silvercart-error-list_content">
                $CustomHtmlFormErrorMessages
            </div>
        </div>
    <% end_if %>

    $CustomHtmlFormFieldByName(emailaddress)
    $CustomHtmlFormFieldByName(password)
    
    <a  href="{$BaseHref}Security/lostpassword"><% _t('Member.BUTTONLOSTPASSWORD') %></a>

    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
    </div>
</form>
