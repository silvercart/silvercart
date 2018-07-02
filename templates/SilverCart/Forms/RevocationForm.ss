<% if $IncludeFormTag %>
    <% if $Customer %>
<form name="SupportRevocationForm" id="SupportRevocationForm" method="post">
    <input type="hidden" name="ExistingOrder" value="" id="SupportExistingOrder">
</form>
<script>
$(document).ready(function() {
    $('select[name="ExistingOrder"]').live('change', function() {
        $('#SupportExistingOrder').val($(this).val());
        $('#SupportRevocationForm').submit();
    });
});
</script>
    <% end_if %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
<% if $Customer %>
    <h4><%t SilverCart\Forms\RevocationForm.Order 'Order' %></h4>
    {$Fields.dataFieldByName(ExistingOrder).FieldHolder}
<% end_if %>
    <h4><%t SilverCart\Forms\RevocationForm.Data 'Data' %></h4>
    <p><%t SilverCart\Dev\Tools.To 'To' %>:<br/>
    <% with $CurrentPage.SiteConfig %>
        <i>
            <strong>{$ShopName}</strong><br/>
            {$ShopStreet} {$ShopStreetNumber}<br/>
            {$ShopPostcode} {$ShopCity}<br/>
            {$ShopCountry.Title}<br/>
        </i>
    <% end_with %>
    </p>
    <p><%t SilverCart\Forms\RevocationForm.RevocationDate 'Date of revocation' %>: <strong>{$CurrentDate}</strong></p>
    {$Fields.dataFieldByName(RevocationOrderData).FieldHolder}
    {$Fields.dataFieldByName(OrderDate).FieldHolder}
    {$Fields.dataFieldByName(OrderNumber).FieldHolder}
    {$Fields.dataFieldByName(Email).FieldHolder}
    <h4><%t SilverCart\Forms\RevocationForm.NameOfConsumer 'Name of the customer' %></h4>
    {$Fields.dataFieldByName(Salutation).FieldHolder}
    {$Fields.dataFieldByName(FirstName).FieldHolder}
    {$Fields.dataFieldByName(Surname).FieldHolder}
    <h4><%t SilverCart\Forms\RevocationForm.AddressOfConsumer 'Address of the customer' %></h4>
    <div class="clearfix">
        {$Fields.dataFieldByName(Street).FieldHolder}
        {$Fields.dataFieldByName(StreetNumber).FieldHolder}
        {$Fields.dataFieldByName(Addition).FieldHolder}
        {$Fields.dataFieldByName(Postcode).FieldHolder}
        {$Fields.dataFieldByName(City).FieldHolder}
        {$Fields.dataFieldByName(Country).FieldHolder}
        {$CustomFormSpecialFields}
    <% loop $Actions %>
        <button class="btn btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Title}">{$Title}</button> 
    <% end_loop %> 
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>