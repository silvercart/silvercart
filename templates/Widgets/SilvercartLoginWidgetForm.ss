<form class="yform full" $FormAttributes>
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    $CustomHtmlFormFieldByName(emailaddress)
    $CustomHtmlFormFieldByName(password)

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </div>

</form>
