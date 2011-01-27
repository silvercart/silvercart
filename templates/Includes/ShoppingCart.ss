<div id="shoppingCart">
    <% if isFilledCart %>
     <table>
        <tr>
            <th>Artikelname</th>
            <th>Einzelpreis</th>
            <th class="Amount">Anzahl</th>
            <th class="Amount">Gesamtpreis</th>
            <th></th>
            <th></th>
        </tr>
        <% control CurrentMember %>
            <% control shoppingCart %>
                <% control positions %>
                <tr>
                    <td><a href="$article.Link">$article.Title</a></td>
                    <td>$article.Price.Nice</td>
                    <td class="Amount">$Quantity</td>
                    <td class="Amount">$Price.Nice</td>
                    <td>$decrementAmountForm $incrementAmountForm</td>
                    <td>$removeFromCartForm</td>
                </tr>
                <% end_control %>
            <tr>
                <td></td>
                <td></td>
                <td>Die Mehrwertsteuer beträgt </td>
                <td class="Amount">$Tax.Nice</td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td><strong>Summe</strong></td>
                <td class="Amount" id="Sum"><strong>$Price.Nice</strong></td>
                <td></td>
                <td></td>
            </tr>
            <% end_control %>
        <% end_control %>
     </table>
    <% else %>
     <p>Ihr Warenkorb ist leer.</p>
    <% end_if %>
</div>
