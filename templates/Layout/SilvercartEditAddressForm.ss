<form class="form" {$FormAttributes} >
    {$CustomHtmlFormMetadata}
    {$CustomHtmlErrorMessages}
    <h4><%t SilvercartAddress.EDITADDRESS %></h4>
<% if $EnablePackstation %>
    <div class="row-fluid">
        <div class="span8">{$CustomHtmlFormFieldByName(IsPackstation,CustomHtmlFormFieldCheckGroup)}</div>
    </div>
<% end_if %>
<% if $EnableBusinessCustomers %>
    <div class="row-fluid">
        <div class="span4">{$CustomHtmlFormFieldByName(IsBusinessAccount,CustomHtmlFormFieldCheck)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(TaxIdNumber)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(Company)}</div>
    </div>
<% end_if %>
    <div class="row-fluid">
        <div class="span4">{$CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(AcademicTitle)}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$CustomHtmlFormFieldByName(FirstName)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(Surname)}</div>
    </div>
    <div class="absolute-address-data">
        <div class="row-fluid">
            <div class="span4">{$CustomHtmlFormFieldByName(Addition)}</div>
            <div class="span4">{$CustomHtmlFormFieldByName(Street)}</div>
            <div class="span4">{$CustomHtmlFormFieldByName(StreetNumber)}</div>
        </div>
    </div>
<% if $EnablePackstation %>
    <div class="packstation-address-data">
        <div class="row-fluid">
            <div class="span4">{$CustomHtmlFormFieldByName(PostNumber)}</div>
            <div class="span4">{$CustomHtmlFormFieldByName(Packstation)}</div>
        </div>
    </div>
<% end_if %>
    <div class="row-fluid">
        <div class="span4">{$CustomHtmlFormFieldByName(Postcode)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(City)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$CustomHtmlFormFieldByName(PhoneAreaCode)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(Phone)}</div>
        <div class="span4">{$CustomHtmlFormFieldByName(Fax)}</div>
    </div>
    {$CustomHtmlFormSpecialFields}
    <hr/>
<% loop $Actions %>
    <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" value="{$Title}" name="{$Name}" type="submit">{$Title}</button>
<% end_loop %>
    <a class="btn btn-small" id="silvercart-edit-address-form-cancel-id" href="{$CancelLink}" title="<%t SilvercartPage.CANCEL %>"><%t SilvercartPage.CANCEL %></a>
</form>
