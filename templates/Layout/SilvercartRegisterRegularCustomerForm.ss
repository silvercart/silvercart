<form class="yform full" $FormAttributes >

    $CustomHtmlFormMetadata

    <fieldset>
        <legend><% _t('SilvercartPage.ADDRESS_DATA') %></legend>
        
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

        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Street)
                 </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(StreetNumber)
                </div>
            </div>
        </div>

        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Postcode)
                 </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(City)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
                </div>
            </div>
        </div>
        
        $CustomHtmlFormFieldByName(Email)

        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(PhoneAreaCode)
                 </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Phone)
                </div>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend><% _t('SilvercartPage.BIRTHDAY') %>:</legend>

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
        <legend><% _t('SilvercartPage.PASSWORD') %></legend>

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
        <legend><% _t('SilvercartPage.TERMSOFSERVICE_PRIVACY') %></legend>

        $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,HasAcceptedTermsAndConditionsFieldCheck)
        $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,HasAcceptedRevocationInstructionFieldCheck)
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

</form>
