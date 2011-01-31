<style type="text/css">
    table, tr, td, th { border: 1px solid; text-align: center; }
    br { padding-bottom: 0.5em; }
    td, th { padding:2px; }
</style>
<h1>Bestellbest&auml;tigung</h1>
Hallo $Salutation $FirstName $Surname,<br />
Ihre Bestellung ist soeben bei uns eingegangen, vielen Dank. Hier die Details:<br />
<% control Order %>
    <% control shippingAddress %>
    <h2>Versandadresse:</h2>
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
                <td>Stra&szlig;e</td><td>$Street $StreetNumber</td>
            </tr>
            <tr>
                <td>Ort</td><td>$Postcode $City</td>
            </tr>
            <tr>
                <td>Land</td><td>$country.Title</td>
            </tr>
        </table>
    <% end_control %>

    <% control invoiceAddress %>
    <h2>Rechnungsadresse:</h2>
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
                <td>Stra&szlig;e</td><td>$Street $StreetNumber</td>
            </tr>
            <tr>
                <td>Ort</td><td>$Postcode $City</td>
            </tr>
            <tr>
                <td>Land</td><td>$country.Title</td>
            </tr>
        </table>
    <% end_control %>
    <h2>Bestellte Artikel:</h2>
    <table>
        <tr>
            <th>Bezeichnung</th><th>Beschreibung</th><th>Anzahl</th><th>Preis</th><th>Summe</th>
        </tr>
    <% control orderPositions %>
    <tr>
        <td>$Title</td><td>$ArticleDescription</td><td>$Quantity</td><td>$Price.Nice</td><td>$PriceTotal.Nice</td>
    </tr>
    <% end_control %>
    </table>
    <h2>Weitere Details:</h2>
    <table>
    <tr>
        <td>Gesamtpreis Artikel</td><td>$PriceTotal.Nice</td>
    </tr>
    <tr>
        <td>Versandart</td><td>$shippingMethod.carrier.Title $shippingMethod.Title</td>
    </tr>
    <tr>
        <td>Versandkosten</td><td>$ShippingCosts.Nice</td>
    </tr>
    <tr>
        <td>Summe</td><td>$AmountTotal.Nice</td>
    </tr>
    </table>
<% end_control %>
<br />
Bitte wenden Sie sich bei R&uuml;ckfragen <a href="mailto:info@pourlatable.de">per Mail</a> an mich: info@pourlatable.de<br />
Ich w&uuml;nsche Ihnen noch einen sch&ouml;nen Tag.<br />
Mit freundlichem Gru&szlig;,<br />
Dorothe Kupper