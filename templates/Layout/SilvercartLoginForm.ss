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

    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
                $Field
            <% end_loop %>
    
            <a class="silvercart-button left" href="{$BaseHref}Security/lostpassword"><% _t('Member.BUTTONLOSTPASSWORD') %></a>
        </div>
    </div>

</form>
