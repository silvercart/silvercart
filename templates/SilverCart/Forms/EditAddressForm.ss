<% if $EnablePackstation %>
<script>
    $(document).ready(function(){
        initAddressForm({$FormName});
    });
</script>
<% end_if %>
<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
<% if $EnablePackstation %>
    <div class="row-fluid">
        <div class="span8">{$Fields.dataFieldByName(IsPackstation).FieldHolder}</div>
    </div>   
<% end_if %>
<% if $EnableBusinessCustomers %>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(IsBusinessAccount).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(TaxIdNumber).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Company).FieldHolder}</div>
    </div>    
<% end_if %>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Salutation).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Company).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(FirstName).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Surname).FieldHolder}</div>
    </div>
    <div class="absolute-address-data">
        <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(Addition).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(Street).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(StreetNumber).FieldHolder}</div>
        </div>   
    </div>
<% if $EnablePackstation %>
    <div class="packstation-address-data">
         <div class="row-fluid">
            <div class="span4">{$Fields.dataFieldByName(PostNumber).FieldHolder}</div>
            <div class="span4">{$Fields.dataFieldByName(Packstation).FieldHolder}</div>
        </div>   
    </div>
<% end_if %>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Postcode).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(City).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Country).FieldHolder}</div>
    </div>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(Phone).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(Fax).FieldHolder}</div>
    </div>
    {$CustomFormSpecialFields}
    <hr/>
<% loop $Actions %>
    <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" name="{$Name}" type="submit">{$Title}</button>
<% end_loop %>
    <a class="btn btn-small" id="silvercart-edit-address-form-cancel-id" href="{$Controller.Link}" title="<%t SilverCart\Model\Pages\Page.CANCEL 'Cancel' %>"><%t SilverCart\Model\Pages\Page.CANCEL 'Cancel' %></a>
<% if $IncludeFormTag %>
</form>
<% end_if %>