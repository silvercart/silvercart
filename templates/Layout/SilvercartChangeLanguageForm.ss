<div class="silvercart-change-language">
    <form class="silvercart-change-language-form" $FormAttributes>
        $CustomHtmlFormMetadata
        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(Language, SilvercartLanguageDropdownField)

        $CustomHtmlFormSpecialFields

        <span>
            <% loop Actions %>
                $Field
            <% end_loop %>
        </span>
    </form>
</div>