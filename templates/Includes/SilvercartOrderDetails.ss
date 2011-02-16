<% control CustomersOrder %>
<h3>Bestelldetails</h3>
    <table>
        <tr>
            <td>Bestelldatum</td><td>$Created.Nice</td>
        </tr>
        <tr>
            <td>Versandkosten</td><td>$SilvercartShippingFeeTotal.Nice</td>
        </tr>
        <tr>
            <td>Bestellwert</td><td>$AmountTotal.Nice</td>
        </tr>
        <tr>
            <td>Bestellstatus</td><td>$status.Title</td>
        </tr>
        <% if Note %>
        <tr>
            <td>Ihre Bemerkung</td><td>$Note</td>
        </tr>
        <% end_if %>
    </table>
<div class="subcolumns">
    <div class="c50l">
        <h3>Lieferadresse</h3>
        <% control SilvercartShippingAddress %>
			<% include AddressTable %>
        <% end_control %>
    </div>
    <div class="c50r">
        <h3>Rechnungsadresse</h3>
        <% control SilvercartInvoiceAddress %>
			<% include AddressTable %>
        <% end_control %>
    </div>
</div>
<h3>Positionen</h3>
    <table>
        <tr>
            <th>Produkttitel</th>
            <th>Produktbeschreibung</th>
            <th>Einzelpreis</th>
            <th>Anzahl</th>
            <th>Summe</th>
        </tr>
        <% control SilvercartOrderPositions %>
        <tr>
            <td>$Title</td>
            <td>$ArticleDescription</td>
            <td>$Price.Nice</td>
            <td>$Amount</td>
            <td>$PriceTotal.Nice</td>
        </tr>
        <% end_control %>
        <tr>
            <td></td>
            <td>Versandkosten</td>
            <td>$SilvercartShippingFeeTotal.Nice</td>
            <td></td>
            <td>$SilvercartShippingFeeTotal.Nice</td>
        </tr>
    </table>
<% end_control %>
