<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <h4><%t SilverCart\Model\Pages\Page.EMAIL_ADDRESS 'Email address' %></h4>
    {$Fields.dataFieldByName(Email).FieldHolder}
<% if $UseMinimumAgeToOrder %>
    <h4><%t SilverCart\Model\Pages\Page.BIRTHDAY 'Birthday' %></h4>
    <div class="row">
        <div class="span3">{$Fields.dataFieldByName(BirthdayDay).FieldHolder}</div>
        <div class="span3">{$Fields.dataFieldByName(BirthdayMonth).FieldHolder}</div>
        <div class="span3">{$Fields.dataFieldByName(BirthdayYear).FieldHolder}</div>
    </div>
<% end_if %>
    <h4><%t SilverCart\Model\Pages\Page.BILLING_ADDRESS 'Invoice address' %></h4>
<% if $EnableBusinessCustomers %>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[IsBusinessAccount]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[TaxIdNumber]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Company]).FieldHolder}</div>
    </div>    
    <hr>
<% end_if %>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Salutation]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[AcademicTitle]).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[FirstName]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Surname]).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Street]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[StreetNumber]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Addition]).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Postcode]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[City]).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Country]).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(InvoiceAddress[Phone]).FieldHolder}</div>
    </div>

    <h4><%t SilverCart\Model\Pages\Page.SHIPPING_ADDRESS 'Shipping address' %></h4>
    <div class="clearfix">
        {$Fields.dataFieldByName(InvoiceAddressAsShippingAddress).FieldHolder}
    </div>
    
    <div id="ShippingAddressFields" class="clearfix">
<% if $EnableBusinessCustomers %>
        <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[IsBusinessAccount]).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[TaxIdNumber]).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Company]).FieldHolder}</div>
        </div>
<% end_if %>
<% if $EnablePackstation %>
        <div class="clearfix">
            {$Fields.dataFieldByName(ShippingAddress[IsPackstation]).FieldHolder}
        </div>
<% end_if %>
        <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Salutation]).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[AcademicTitle]).FieldHolder}</div>
        </div>
        <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[FirstName]).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Surname]).FieldHolder}</div>
        </div>
        <div class="absolute-address-data">
            <div class="row-fluid">
                <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Street]).FieldHolder}</div>
                <div class="span4">{$Fields.dataFieldByName(ShippingAddress[StreetNumber]).FieldHolder}</div>
                <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Addition]).FieldHolder}</div>
            </div>    
        </div>
<% if $EnablePackstation %>
        <div class="packstation-address-data">
            <div class="row-fluid">
                <div class="span4">{$Fields.dataFieldByName(ShippingAddress[PostNumber]).FieldHolder}</div>
                <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Packstation]).FieldHolder}</div>
            </div>    
        </div>   
<% end_if %>
        <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Postcode]).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[City]).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Country]).FieldHolder}</div>
        </div>
        <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(ShippingAddress[Phone]).FieldHolder}</div>
        </div>
    </div>
    {$CustomFormSpecialFields}
    <hr>
    <div class="clearfix">
<% loop $Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
<% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>