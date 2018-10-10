<% if $TaxIdNumber %>
    {$fieldLabel('TaxIdNumber')}: {$TaxIdNumber}<br/>
<% end_if %>
<% if $Company %>
    {$fieldLabel('Company')}: {$Company}<br/>
<% end_if %>
    {$Salutation} {$AcademicTitle} {$FullName}<br/>
<% if $Addition %>
    {$Addition}<br/>
<% end_if %>
<% if $IsPackstation == 1 %>
    {$fieldLabel('PostNumber')}: {$PostNumber}<br/>
    {$fieldLabel('PackstationPlain')}: {$Packstation}<br/>
<% else %>
    {$Street} {$StreetNumber}<br/>
<% end_if %>
    {$Postcode} {$City}<br/>
    {$Country.Title}<br/>
<% if $Phone %>
    <br/>{$fieldLabel('PhoneShort')}: {$Phone}
<% end_if %>
<% if $Fax %>
    <br/>{$fieldLabel('Fax')}: {$Fax}
<% end_if %>