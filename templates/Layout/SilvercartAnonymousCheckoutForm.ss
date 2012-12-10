<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    <fieldset>
        <legend><% _t('SilvercartPage.ADDRESSINFORMATION','address information') %></legend>
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
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Street)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(StreetNumber)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Addition)
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
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Email)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(PhoneAreaCode)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Phone)
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><% _t('SilvercartPage.SHIPPING_AND_BILLING','shipping and billing address')%></legend>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(PaymentMethod,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    <div id="ShippingMethod">
                        $CustomHtmlFormFieldByName(ShippingMethod,CustomHtmlFormFieldSelect)
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><% _t('SilvercartPage.REMARKS','REMARKS') %></legend>
        <div class="subcolumns">
            $CustomHtmlFormFieldByName(Note)
        </div>
    </fieldset>
    <fieldset>
        <legend><% _t('SilvercartPage.TERMSOFSERVICE_PRIVACY','terms of service and privacy statement') %></legend>
        $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,SilvercartHasAcceptedTermsAndConditionsFieldCheck)
        $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,SilvercartHasAcceptedRevocationInstructionFieldCheck)
        $CustomHtmlFormFieldByName(SubscribedToNewsletter,CustomHtmlFormFieldCheck)
    </fieldset>

    $CustomHtmlFormSpecialFields

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>
</form>