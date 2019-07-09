{$fieldLabel('TaxIdNumber')}: <% if $TaxIdNumber %>{$TaxIdNumber}<% else %>---<% end_if %><br/>
{$fieldLabel('Company')}: <% if $Company %>{$Company}<% else %>---<% end_if %><br/>
{$fieldLabel('Salutation')}: <% if $Salutation %>{$Salutation}<% else %>---<% end_if %><br/>
{$fieldLabel('AcademicTitle')}: <% if $AcademicTitle %>{$AcademicTitle}<% else %>---<% end_if %><br/>
{$fieldLabel('FirstName')}: <% if $FirstName %>{$FirstName}<% else %>---<% end_if %><br/>
{$fieldLabel('Surname')}: <% if $Surname %>{$Surname}<% else %>---<% end_if %><br/>
<% if $IsPackstation == 1 %>
{$fieldLabel('PostNumber')}: <% if $PostNumber %>{$PostNumber}<% else %>---<% end_if %><br/>
{$fieldLabel('PackstationPlain')}: <% if $Packstation %>{$Packstation}<% else %>---<% end_if %><br/>
<% else %>
{$fieldLabel('Street')}: <% if $Street %>{$Street}<% else %>---<% end_if %><br/>
{$fieldLabel('StreetNumber')}: <% if $StreetNumber %>{$StreetNumber}<% else %>---<% end_if %><br/>
<% end_if %>
{$fieldLabel('Addition')}: <% if $Addition %>{$Addition}<% else %>---<% end_if %><br/>
{$fieldLabel('Postcode')}: <% if $Postcode %>{$Postcode}<% else %>---<% end_if %><br/>
{$fieldLabel('City')}: <% if $City %>{$City}<% else %>---<% end_if %><br/>
{$fieldLabel('Country')}: <% if $Country %>{$Country.Title}<% else %>---<% end_if %><br/>
{$fieldLabel('Phone')}: <% if $Phone %>{$Phone}<% else %>---<% end_if %><br/>
{$fieldLabel('Fax')}: <% if $Fax %>{$Fax}<% else %>---<% end_if %><br/>