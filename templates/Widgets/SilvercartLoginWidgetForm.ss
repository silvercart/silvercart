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

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </div>

</form>
