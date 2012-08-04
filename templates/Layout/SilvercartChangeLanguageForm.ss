<div class="silvercart-change-language">
    <form class="silvercart-change-language-form" $FormAttributes>
        $CustomHtmlFormMetadata
        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(Language, SilvercartLanguageDropdownField)

        <div id="silvercart-quicklogin-form-actions">
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
    </form>

</div>