<div class="silvercart-change-language">
    <form class="silvercart-change-language-form" $FormAttributes>
        $CustomHtmlFormMetadata
        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(Language, SilvercartLanguageDropdownField)

        $CustomHtmlFormSpecialFields

        <div id="silvercart-quicklogin-form-actions">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </form>

</div>