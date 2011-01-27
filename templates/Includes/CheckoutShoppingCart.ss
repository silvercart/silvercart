<div id="shoppingCart">
    <% if isFilledCart %>
    <table>
            <tr>
                <th>Artikelname</th>
                <th>Einzelpreis</th>
                <th class="Amount">Anzahl</th>
                <th class="Amount">Gesamtpreis</th>
            </tr>
            <% control CurrentMember %>
                <% control shoppingCart %>
                    <% control orderCartPositions %>
                    <tr>
                        <td><a href="$article.Link">$article.Title</a></td>
                        <td>$article.Price.Nice</td>
                        <td class="Amount">$Quantity</td>
                        <td class="Amount">$Price &euro;</td>
                    </tr>
                    <% end_control %>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Die Mehrwertsteuer beträgt </td>
                    <td class="Amount"><input id="MWST" readOnly="true" value="$getTaxFormatted &euro;"></td>
                </tr>

                <tr class="ShippingCosts" title="ShippingCost ">
                    <td></td>
                    <td></td>
                    <td>Die Lieferkosten betragen </td>
                    <td class="Amount"><input id="Shipping_Cost" readOnly="true" value=" &euro;"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><strong>Summe</strong></td>
                    <td class="Amount" id="Sum"><strong><input id="Sum_Price" value="$PriceFormatted &euro;"readOnly="true"></strong></td>
                </tr>
                <% end_control %>
            <% end_control %>
        </table>
    <% else %>
    <p>Ihr Warenkorb ist leer.</p>
    <% end_if %>
</div>