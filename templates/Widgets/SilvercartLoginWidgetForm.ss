<form class="yform full" $FormAttributes>
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

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </div>

</form>
