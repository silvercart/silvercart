<table id="silvercart-invoice-address-table-id" summary="Your invoice address data">
    <tbody>
        <tr>
            <td><strong><% _t('SilvercartAddress.FIRSTNAME','Firstname') %></strong></td>
            <td id="silvercart-invoice-address-table-firstname-id">$FirstName</td>
        </tr>
        <tr>
            <td><strong><% _t('SilvercartAddress.SURNAME','Surname') %></strong></td>
            <td id="silvercart-invoice-address-table-surname-id">$Surname</td>
        </tr>
        <% if Addition %>
        <tr>
            <td><strong><% _t('SilvercartAddress.ADDITION','Addition') %></strong></td>
            <td id="silvercart-invoice-address-table-addition-id">$Addition</td>
        </tr>
        <% end_if %>
        <tr>
            <td><strong><% _t('SilvercartAddress.STREET','Street') %></strong></td>
            <td id="silvercart-invoice-address-table-street-id">$Street</td>
        </tr>
        <tr>
            <td><strong><% _t('SilvercartAddress.STREETNUMBER','Streetnumber') %></strong></td>
            <td id="silvercart-invoice-address-table-streetnumber-id">$StreetNumber</td>
        </tr>
        <tr>
            <td><strong><% _t('SilvercartAddress.POSTCODE','Postcode') %></strong></td>
            <td id="silvercart-invoice-address-table-postcode-id">$Postcode</td>
        </tr>
        <tr>
            <td><strong><% _t('SilvercartAddress.CITY','City') %></strong></td>
            <td id="silvercart-invoice-address-table-city-id">$City</td>
        </tr>
        <tr>
            <td><strong><% _t('SilvercartAddress.PHONE','Phone') %></strong></td>
            <td id="silvercart-invoice-address-table-phone-id">
                <% if Phone %>
                    {$PhoneAreaCode}/{$Phone}
                <% else %>
                    ---
                <% end_if %>
            </td>
        </tr>
        <tr>
            <td><strong><% _t('SilvercartCountry.SINGULARNAME','Country') %></strong></td>
            <td id="silvercart-invoice-address-table-country-id">$SilvercartCountry.Title</td>
        </tr>
    </tbody>
</table>