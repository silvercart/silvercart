<div class="silvercart-change-language">
    <form class="silvercart-change-language-form" $FormAttributes>
        $CustomHtmlFormMetadata
        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(Language, SilvercartLanguageDropdownField)

        $CustomHtmlFormSpecialFields

        <span>
            <% control Actions %>
                $Field
            <% end_control %>
        </span>
    </form>

</div>