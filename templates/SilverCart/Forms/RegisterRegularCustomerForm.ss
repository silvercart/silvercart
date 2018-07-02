<div class="register">
<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>

<% if $EnableBusinessCustomers %>
    <h4><%t SilverCart\Model\Customer\Customer.BUSINESSCUSTOMER 'Business customer' %></h4>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(IsBusinessAccount).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(TaxIdNumber).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Company).FieldHolder}</div>
    </div>
<% end_if %>
    <h4><%t SilverCart\Model\Pages\Page.ADDRESS_DATA 'Address data' %></h4>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Salutation).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(AcademicTitle).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(FirstName).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Surname).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Street).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(StreetNumber).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Addition).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Postcode).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(City).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Country).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Phone).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Fax).FieldHolder}</div>
    </div>
<% if $demandBirthdayDate %>
    <h4><%t SilverCart\Model\Pages\Page.BIRTHDAY 'Birthday' %></h4>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(BirthdayDay).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(BirthdayMonth).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(BirthdayYear).FieldHolder}</div>
    </div>
<% end_if %>
    <h4><%t SilverCart\Model\Pages\Page.ACCESS_CREDENTIALS 'Access Credentials' %></h4>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Email).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(EmailCheck).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Password).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(PasswordCheck).FieldHolder}</div>
    </div>
    <h4><%t SilverCart\Model\Pages\RegistrationPage.OTHERITEMS 'Miscellaneous' %></h4>
    <div class="row-fluid">
        <div class="span12">{$Fields.dataFieldByName(SubscribedToNewsletter).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span12 last">{$CustomFormSpecialFields}</div>
    </div>
    <div class="control-group">
    <% loop $Actions %>
        <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" name="{$Name}" type="submit">{$Title} <span class="icon icon-caret-right"></span></button>
    <% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>
</div>