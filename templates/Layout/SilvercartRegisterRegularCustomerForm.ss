<div class="register">
    <form class="form-horizontal" $FormAttributes >
        {$CustomHtmlFormMetadata}
        $CustomHtmlFormFieldByName(backlink,CustomHtmlFormFieldHidden)
        <h4><% _t('SilvercartPage.ADDRESS_DATA') %></h4>
    <% if EnableBusinessCustomers %>
        $CustomHtmlFormFieldByName(IsBusinessAccount,CustomHtmlFormFieldCheck)
        $CustomHtmlFormFieldByName(TaxIdNumber)
        $CustomHtmlFormFieldByName(Company)
    <% end_if %>
    
        $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(FirstName)
        $CustomHtmlFormFieldByName(Surname)
        $CustomHtmlFormFieldByName(Addition)
        $CustomHtmlFormFieldByName(Street)
        $CustomHtmlFormFieldByName(StreetNumber)
        $CustomHtmlFormFieldByName(Postcode)
        $CustomHtmlFormFieldByName(City)
        $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(PhoneAreaCode)
        $CustomHtmlFormFieldByName(Phone)
        $CustomHtmlFormFieldByName(Fax)

    <% if demandBirthdayDate %>
        <h4><% _t('SilvercartPage.BIRTHDAY') %>:</h4>
        $CustomHtmlFormFieldByName(BirthdayDay,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(BirthdayMonth,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(BirthdayYear)
    <% end_if %>

        <h4><% _t('SilvercartPage.ACCESS_CREDENTIALS') %></h4>
        $CustomHtmlFormFieldByName(Email)
        $CustomHtmlFormFieldByName(EmailCheck)
        $CustomHtmlFormFieldByName(Password)
        $CustomHtmlFormFieldByName(PasswordCheck)
        <h4><% _t('SilvercartRegistrationPage.OTHERITEMS') %></h4>
        $CustomHtmlFormFieldByName(SubscribedToNewsletter,SilvercartHasAcceptedNewsletterFieldCheck)
        {$CustomHtmlFormSpecialFields}
        <hr>
        <div class="margin-side clearfix">
        <% loop Actions %>
            <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
        <% end_loop %>
        </div>
    </form>
</div>