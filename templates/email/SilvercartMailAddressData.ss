
<table>
<% if $TaxIdNumber %>
    <tr>
        <td>$fieldLabel(TaxIdNumber)</td>
        <td>$TaxIdNumber</td>
    </tr>
<% end_if %>
<% if $Company %>
    <tr>
        <td>$fieldLabel(Company)</td>
        <td>$Company</td>
    </tr>
<% end_if %>
<% if $FirstName || $Surname %>
    <% if $Salutation %>
    <tr>
        <td>$fieldLabel(Salutation)</td>
        <td>$SalutationText</td>
    </tr>
    <% end_if %>
    <% if $AcademicTitle %>
    <tr>
        <td>$fieldLabel(AcademicTitle)</td>
        <td>$AcademicTitle</td>
    </tr>
    <% end_if %>
    <tr>
        <td>$fieldLabel(FirstName)</td>
        <td>$FirstName</td>
    </tr>
    <tr>
        <td>$fieldLabel(Surname)</td>
        <td>$Surname</td>
    </tr>
<% end_if %>
<% if Addition %>
    <tr>
        <td>$fieldLabel(Addition)</td>
        <td>$Addition</td>
    </tr>
<% end_if %>
<% if IsPackstation %>
    <tr>
        <td>$fieldLabel(PostNumber)</td>
        <td>$PostNumber</td>
    </tr>
    <tr>
        <td>$fieldLabel(PackstationPlain)</td>
        <td>$Packstation</td>
    </tr>
<% else %>
    <tr>
        <td>$fieldLabel(Street)</td>
        <td>$Street $StreetNumber</td>
    </tr>
<% end_if %>
    <tr>
        <td>$fieldLabel(Postcode)</td>
        <td>$Postcode</td>
    </tr>
    <tr>
        <td>$fieldLabel(City)</td>
        <td>$City</td>
    </tr>
    <tr>
        <td>$fieldLabel(Phone)</td>
        <td><% if Phone %>{$PhoneAreaCode} {$Phone}<% else %>---<% end_if %></td>
    </tr>
    <tr>
        <td>$fieldLabel(Fax)</td>
        <td>$Fax</td>
    </tr>
    <tr>
        <td>$fieldLabel(SilvercartCountry)</td>
        <td>$SilvercartCountry.Title</td>
    </tr>
</table>