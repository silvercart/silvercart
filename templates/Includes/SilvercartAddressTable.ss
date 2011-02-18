<table>
    <tr>
        <td>Vorname</td><td>$FirstName</td>
    </tr>
    <tr>
        <td>Nachname</td><td>$Surname</td>
    </tr>
    <% if Addition %>
    <tr>
        <td>Adresszusatz</td><td>$Addition</td>
    </tr>
    <% end_if %>
    <tr>
        <td>Stra&szlig;e</td><td>$Street</td>
    </tr>
    <tr>
        <td>Hausnummer</td><td>$StreetNumber</td>
    </tr>
    <tr>
        <td>PLZ</td><td>$Postcode</td>
    </tr>
    <tr>
        <td>Stadt</td><td>$City</td>
    </tr>
    <tr>
        <td>Telefonnummer</td><td>
            <% if Phone %>
                {$PhoneAreaCode}/{$Phone}
            <% else %>
                ---
            <% end_if %>
        </td>
    </tr>
    <tr>
        <td>Land</td><td>$SilvercartCountry.Title</td>
    </tr>
</table>