<% if isFilledCart %>
    <div class="widget">
        <div class="widget_content">
            <h2>Warenkorb</h2>
                <table>
                    <tr>
                        <th>Artikelname</th>
                        <th class="Amount">Anzahl</th>
                        <th class="pricewidth">Preis </th>
                    </tr>
                    <% control CurrentMember %>
                        <% control shoppingCart %>
                            <% control positions %>
                            <tr>
                                <td><a href="$article.Link">$article.Title</a></td>
                                <td class="Amount">$Quantity</td>
                                <td class="pricewidth">$Price.Nice</td>
                            </tr>
                            <% end_control %>
                                <tr>
                                    <td></td>
                                    <td>Summe</td>
                                    <td class="pricewidth">$Price.Nice</td>
                                </tr>
                        <% end_control %>
                    <% end_control %>
                </table>
            <a href="{$baseHref}warenkorb"><strong class="ShoppingCart">zum Warenkorb</strong></a>
        </div>
    </div>
<% end_if %>
