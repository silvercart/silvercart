<form class="form-horizontal grouped" $FormAttributes >
    $CustomHtmlFormMetadata

    <h4><% _t('SilvercartPage.ADDRESS_DATA') %></h4>
    <div class="control-group">
        <label class="control-label" for=""><% _t('SilvercartMyAccountHolder.YOUR_CUSTOMERNUMBER') %></label>
        <div class="controls">
            <strong class="value">$CurrentMember.CustomerNumber</strong>
        </div>
    </div>
    
    $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
    $CustomHtmlFormFieldByName(FirstName)
    $CustomHtmlFormFieldByName(Surname)
    $CustomHtmlFormFieldByName(Email)

    <% if demandBirthdayDate %>
        <h4><% _t('SilvercartPage.BIRTHDAY') %></h4>
        $CustomHtmlFormFieldByName(BirthdayDay,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(BirthdayMonth,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(BirthdayYear)
    <% end_if %>

    <h4><% _t('SilvercartPage.PASSWORD') %></h4>
    <div class="alert alert-info">
        <p><% _t('SilvercartPage.PASSWORD_CASE_EMPTY','If You leave this field empty, Your password will not be changed.') %></p>
    </div>

    $CustomHtmlFormFieldByName(Password)
    $CustomHtmlFormFieldByName(PasswordCheck)

    <h4><% _t('SilvercartPage.NEWSLETTER') %></h4>
    $CustomHtmlFormFieldByName(SubscribedToNewsletter,SilvercartHasAcceptedNewsletterFieldCheck)

    $CustomHtmlFormSpecialFields

    <hr>
    <div class="control-group">
    <% loop Actions %>
        <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" value="{$Title}" name="{$Name}" type="submit">{$Title}</button>
    <% end_loop %>
    </div>
</form>
