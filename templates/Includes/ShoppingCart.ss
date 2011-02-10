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
        <% control positions %>
        <tr>
            <td><a href="$article.Link">$article.Title</a></td>
            <td>$article.Price.Nice</td>
            <td class="Amount">$Quantity</td>
            <td class="Amount">$Price.Nice</td>
            <td>$IncrementPositionQuantityForm $DecrementPositionQuantityForm</td>
            <td>$RemovePositionForm</td>
        </tr>
        <% control article %>
        <% end_control %>
        <% end_control %>
        <tr>
            <td></td>
            <td></td>
            <td><% _t('Page.INCLUDED_VAT') %></td>
            <td class="Amount"></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td><strong>Summe</strong></td>
            <td class="Amount" id="Sum"><strong><% control CurrentMember %>$shoppingCart.AmountTotal.Nice<% end_control %></strong></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <% else %>
    <p>Ihr Warenkorb ist leer.</p>
    <% end_if %>
</div>
