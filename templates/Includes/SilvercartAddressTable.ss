<table>
    <tr>
        <td><strong><% _t('SilvercartAddress.FIRSTNAME','Firstname') %></strong></td>
        <td>$FirstName</td>
    </tr>
    <tr>
        <td><strong><% _t('SilvercartAddress.SURNAME','Surname') %></strong></td>
        <td>$Surname</td>
    </tr>
    <% if Addition %>
    <tr>
        <td><strong><% _t('SilvercartAddress.ADDITION','Addition') %></strong></td>
        <td>$Addition</td>
    </tr>
    <% end_if %>
    <tr>
        <td><strong><% _t('SilvercartAddress.STREET','Street') %></strong></td>
        <td>$Street</td>
    </tr>
    <tr>
        <td><strong><% _t('SilvercartAddress.STREETNUMBER','Streetnumber') %></strong></td>
        <td>$StreetNumber</td>
    </tr>
    <tr>
        <td><strong><% _t('SilvercartAddress.POSTCODE','Postcode') %></strong></td>
        <td>$Postcode</td>
    </tr>
    <tr>
        <td><strong><% _t('SilvercartAddress.CITY','City') %></strong></td>
        <td>$City</td>
    </tr>
    <tr>
        <td><strong><% _t('SilvercartAddress.PHONE','Phone') %></strong></td>
        <td>
            <% if Phone %>
                {$PhoneAreaCode}/{$Phone}
            <% else %>
                ---
            <% end_if %>
        </td>
    </tr>
    <tr>
        <td><strong><% _t('SilvercartCountry.SINGULARNAME','Country') %></strong></td>
        <td>$SilvercartCountry.Title</td>
    </tr>
</table>