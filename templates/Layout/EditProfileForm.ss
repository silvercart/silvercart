<% if IncludeFormTag %>
    <form class="yform" $FormAttributes >
<% end_if %>

    $CustomHtmlFormMetadata


    <fieldset>
        <legend><% _t('Page.ADDRESS_DATA','address data') %></legend>

               $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)

        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(FirstName)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Surname)
                </div>
            </div>
        </div>

        $CustomHtmlFormFieldByName(Email)

    </fieldset>


    <fieldset>
        <legend><% _t('Page.BIRTHDAY','birthday') %>:</legend>

        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(BirthdayDay,CustomHtmlFormFieldSelect)
                 </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(BirthdayMonth,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(BirthdayYear)
                </div>
            </div>
        </div>

    </fieldset>

        <fieldset>
        <legend><% _t('Page.PASSWORD') %></legend>
        <div>
            <p><% _t('Page.PASSWORD_CASE_EMPTY','If You leave this field empty, Your password will not be changed.') %></p>
        </div>

        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Password)
                 </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(PasswordCheck)
                </div>
            </div>
        </div>

    </fieldset>
 <fieldset>
        <legend><% _t('Page.NEWSLETTER','newsletter') %></legend>

        $CustomHtmlFormFieldByName(SubscribedToNewsletter,CustomHtmlFormFieldCheck)

    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>   


    $dataFieldByName(SecurityID)

    <% if IncludeFormTag %>
</form>
<% end_if %>
