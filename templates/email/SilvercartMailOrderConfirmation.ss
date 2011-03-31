        <style type="text/css">
            table { width:auto; border-collapse:collapse; margin-bottom:0.5em; border-top:0px; border-bottom:0px; }
            table caption { font-variant:small-caps; }
            table.full { width:100%; }
            table.fixed { table-layout:fixed; }

            th,td { padding:0.5em; }
            thead th { border-bottom:1px #666 solid; }
            tbody th { background:#e0e0e0; color:#666; }
            tbody th[scope="row"], tbody th.sub { background:#f0f0f0; }

            tbody th { border-bottom:1px solid #fff; text-align:left; }
            tbody td { border-bottom:1px solid #eee; }
            tbody tr.new.block td {  }

            tfoot td {border-top: 1px #666 solid; }
        </style>
        <h1>Bestellbest&auml;tigung</h1>

        <p>Hallo $Salutation $FirstName $Surname,</p>
        <p>Ihre Bestellung ist soeben bei uns eingegangen, vielen Dank.</p>
        <p>Hier die Details:</p>
        <% control SilvercartOrder %>
        <table>
            <tr>
                <td>Bestelldatum</td>
                <td>$Created.Nice</td>
            </tr>
            <tr>
                <td>Bestellstatus</td>
                <td>$SilvercartOrderStatus.Title</td>
            </tr>
            <% if Note %>
            <tr>
                <td>Ihre Bemerkung</td>
                <td>$FormattedNote</td>
            </tr>
            <% end_if %>
        </table>

        <table>
            <tbody>
                <tr>
                    <td>
                        <h2>Versandadresse:</h2>
                        <% control SilvercartShippingAddress %>
                        <table>
                            <tr>
                                <td>Vorname</td>
                                <td>$FirstName</td>
                            </tr>
                            <tr>
                                <td>Nachname</td>
                                <td>$Surname</td>
                            </tr>
                            <% if Addition %>
                            <tr>
                                <td>Adresszusatz</td>
                                <td>$Addition</td>
                            </tr>
                            <% end_if %>
                            <tr>
                                <td>Stra&szlig;e</td>
                                <td>$Street</td>
                            </tr>
                            <tr>
                                <td>Hausnummer</td>
                                <td>$StreetNumber</td>
                            </tr>
                            <tr>
                                <td>PLZ</td>
                                <td>$Postcode</td>
                            </tr>
                            <tr>
                                <td>Stadt</td>
                                <td>$City</td>
                            </tr>
                            <tr>
                                <td>Telefonnummer</td>
                                <td><% if Phone %>{$PhoneAreaCode}/{$Phone}<% else %>---<% end_if %></td>
                            </tr>
                            <tr>
                                <td>Land</td>
                                <td>$SilvercartCountry.Title</td>
                            </tr>
                        </table>
                        <% end_control %>
                    </td>
                    <td>
                        <h2>Rechnungsadresse:</h2>
                        <% control SilvercartInvoiceAddress %>
                        <table>
                            <tr>
                                <td>Vorname</td>
                                <td>$FirstName</td>
                            </tr>
                            <tr>
                                <td>Nachname</td>
                                <td>$Surname</td>
                            </tr>
                            <% if Addition %>
                            <tr>
                                <td>Adresszusatz</td>
                                <td>$Addition</td>
                            </tr>
                            <% end_if %>
                            <tr>
                                <td>Stra&szlig;e</td>
                                <td>$Street</td>
                            </tr>
                            <tr>
                                <td>Hausnummer</td>
                                <td>$StreetNumber</td>
                            </tr>
                            <tr>
                                <td>PLZ</td>
                                <td>$Postcode</td>
                            </tr>
                            <tr>
                                <td>Stadt</td>
                                <td>$City</td>
                            </tr>
                            <tr>
                                <td>Telefonnummer</td>
                                <td><% if Phone %>{$PhoneAreaCode}/{$Phone}<% else %>---<% end_if %></td>
                            </tr>
                            <tr>
                                <td>Land</td>
                                <td>$SilvercartCountry.Title</td>
                            </tr>
                        </table>
                        <% end_control %>
                    </td>
                </tr>
            </tbody>
        </table>

        <h2>Bestellte Artikel:</h2>
        <table>
            <thead>
                <tr>
                    <th>Artikelname</th>
                    <th class="right">Einzelpreis</th>
                    <th class="right">MwSt.</th>
                    <th class="right">Anzahl</th>
                    <th class="right">Preis</th>
                </tr>
            </thead>
            <tbody>
                <% control SilvercartOrderPositions(TaxRate > 0) %>
                <tr class="$EvenOrOdd">
                    <td>$Title</td>
                    <td class="right">$Price.Nice</td>
                    <td class="right">{$TaxRate}%</td>
                    <td class="right">$Quantity</td>
                    <td class="right">$PriceTotal.Nice</td>
                </tr>
                <% end_control %>

                <tr class="new-block">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="right"><strong>Zwischensumme</strong></td>
                    <td class="right"><strong>$TaxableAmountGross.Nice</strong></td>
                </tr>

                <% if TaxRatesWithoutFees %>
                <% control TaxRatesWithoutFees %>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Darin enthaltene Mehrwertsteuer ({$Rate}%)</td>
                    <td class="right">$Amount.Nice</td>
                </tr>
                <% end_control %>
                <% end_if %>

                <tr>
                    <td>Versandart</td>
                    <td colspan="3"><strong>$CarrierAndShippingMethodTitle</strong></td>
                    <td class="right">$HandlingCostShipment.Nice</td>
                </tr>
                <tr>
                    <td>Bezahlart</td>
                    <td colspan="3"><strong>$PaymentMethodTitle</strong></td>
                    <td class="right">$HandlingCostPayment.Nice</td>
                </tr>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="right"><strong>Zwischensumme</strong></td>
                    <td class="right"><strong>$TaxableAmountGrossWithFees.Nice</strong></td>
                </tr>

                <% if TaxRatesWithFees %>
                <% control TaxRatesWithFees %>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Darin enthaltene Mehrwertsteuer ({$Rate}%)</td>
                    <td class="right">$Amount.Nice</td>
                </tr>
                <% end_control %>
                <% end_if %>

                <% control SilvercartOrderPositions(TaxRate = 0) %>
                <tr>
                    <td>$Title</td>
                    <td class="right">$Price.Nice</td>
                    <td class="right"></td>
                    <td class="right">$Quantity</td>
                    <td class="right">$PriceTotal.Nice</td>
                </tr>
                <% end_control %>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="right"><strong>Gesamt</strong></td>
                    <td class="right"><strong>$AmountTotal.Nice</strong></td>
                </tr>
            </tbody>
        </table>
        <% end_control %>

        <p>Mit freundlichem Gru&szlig;,</p>
        <p>Ihr SilverCart Webshop Team</p>