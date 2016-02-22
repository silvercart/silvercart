<form class="form-horizontal grouped" $FormAttributes >
    {$CustomHtmlFormMetadata}
    {$CustomHtmlErrorMessages}
    <h4><% _t('SilvercartAddress.EDITADDRESS') %></h4>
    <% if EnablePackstation %>
        $CustomHtmlFormFieldByName(IsPackstation,CustomHtmlFormFieldCheckGroup)
    <% end_if %>
    <% if EnableBusinessCustomers %>
        $CustomHtmlFormFieldByName(IsBusinessAccount,CustomHtmlFormFieldCheck)
        $CustomHtmlFormFieldByName(TaxIdNumber)
        $CustomHtmlFormFieldByName(Company)
        <hr>
    <% end_if %>

    $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
    $CustomHtmlFormFieldByName(FirstName)
    $CustomHtmlFormFieldByName(Surname)
    <div class="absolute-address-data">
        $CustomHtmlFormFieldByName(Addition)
        $CustomHtmlFormFieldByName(Street)
        $CustomHtmlFormFieldByName(StreetNumber)
    </div>
    <% if EnablePackstation %>
        <div class="packstation-address-data">
            $CustomHtmlFormFieldByName(PostNumber)
            $CustomHtmlFormFieldByName(Packstation)
        </div>
    <% end_if %>
    $CustomHtmlFormFieldByName(Postcode)
    $CustomHtmlFormFieldByName(City)
    $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)

    $CustomHtmlFormFieldByName(PhoneAreaCode)
    $CustomHtmlFormFieldByName(Phone)
    $CustomHtmlFormFieldByName(Fax)

    $CustomHtmlFormSpecialFields

    <% loop Actions %>
        <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" value="{$Title}" name="{$Name}" type="submit">{$Title}</button>
    <% end_loop %>
    <a class="btn btn-small" id="silvercart-edit-address-form-cancel-id" href="{$CancelLink}" title="<% _t('SilvercartPage.CANCEL') %>"><% _t('SilvercartPage.CANCEL') %></a>
</form>
