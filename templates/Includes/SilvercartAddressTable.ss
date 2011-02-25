<table>
    <tr>
        <td><% _t('SilvercartAddress.FIRSTNAME','Firstname') %></td><td>$FirstName</td>
    </tr>
    <tr>
        <td><% _t('SilvercartAddress.SURNAME','Surname') %></td><td>$Surname</td>
    </tr>
    <% if Addition %>
    <tr>
        <td><% _t('SilvercartAddress.ADDITION','Addition') %></td><td>$Addition</td>
    </tr>
    <% end_if %>
    <tr>
        <td><% _t('SilvercartAddress.STREET','Street') %></td><td>$Street</td>
    </tr>
    <tr>
        <td><% _t('SilvercartAddress.STREETNUMBER','Streetnumber') %></td><td>$StreetNumber</td>
    </tr>
    <tr>
        <td><% _t('SilvercartAddress.POSTCODE','Postcode') %></td><td>$Postcode</td>
    </tr>
    <tr>
        <td><% _t('SilvercartAddress.CITY','City') %></td><td>$City</td>
    </tr>
    <tr>
        <td><% _t('SilvercartAddress.PHONE','Phone') %></td><td>
            <% if Phone %>
                {$PhoneAreaCode}/{$Phone}
            <% else %>
                ---
            <% end_if %>
        </td>
    </tr>
    <tr>
        <td><% _t('SilvercartCountry.SINGULARNAME','Country') %></td><td>$SilvercartCountry.Title</td>
    </tr>
</table>