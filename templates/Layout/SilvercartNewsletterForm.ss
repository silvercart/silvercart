<form class="yform full" $FormAttributes >

      $CustomHtmlFormMetadata

      <fieldset>
        <legend><% _t('SilvercartPage.NEWSLETTER_FORM','Newsletter form') %></legend>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(FirstName)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Surname)
                </div>
            </div>
        </div>
        $CustomHtmlFormFieldByName(Email)
        $CustomHtmlFormFieldByName(NewsletterAction,CustomHtmlFormFieldCheck)
    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
                $Field
            <% end_control %>
        </div>
    </div>
</form>
