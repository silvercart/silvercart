<address class="d-inline-block bg-white text-secondary font-italic font-weight-light letter-spacing-1 font-big-4 px-3 py-2 mx-0 mx-md-1 my-1 border w-lg-auto w-100">
<% if $HeadLine %>
    <strong>{$HeadLine}</strong><br/>
<% end_if %>
    {$BeforeAddressContent}
<% if $TaxIdNumber %>
    {$fieldLabel('TaxIdNumber')}: {$TaxIdNumber}<br/>
<% end_if %>
<% if $Company %>
    {$fieldLabel('Company')}: {$Company}<br/>
<% end_if %>
    {$fieldLabel('Name')}: {$Salutation} {$AcademicTitle} {$FullName}<br/>
<% if $Addition %>
    {$Addition}<br/>
<% end_if %>
<% if $IsPackstation == 1 %>
    {$fieldLabel('PostNumber')}: {$PostNumber}<br/>
    {$fieldLabel('PackstationPlain')}: {$Packstation}<br/>
<% else %>
    {$Street} {$StreetNumber}<br/>
<% end_if %>
    {$Country.ISO2}-{$Postcode} {$City}<br/>
<% if $Phone %>
    {$fieldLabel('PhoneShort')}: {$Phone}<br/>
<% end_if %>
<% if $Fax %>
    {$fieldLabel('Fax')}: {$Fax}<br/>
<% end_if %>
    {$AfterAddressContent}
</address>