<style type="text/css">
    table, tr, td, th { border: 1px solid; text-align: center; }
    br { padding-bottom: 0.5em; }
    td, th { padding:2px; }
</style>
<h1>Bestellbest&auml;tigung</h1>

<p>Hallo $Salutation $FirstName $Surname</p>
<p>Ihre Bestellung ist soeben bei uns eingegangen, vielen Dank. Hier die Details:</p>

<% control SilvercartOrder %>
   <table class="full">
       <tr>
           <td>Bestelldatum</td>
           <td>$Created.Nice</td>
       </tr>
       <tr>
           <td>Bestellstatus</td>
           <td>$status.Title</td>
       </tr>
       <% if Note %>
           <tr>
               <td valign="top">Ihre Bemerkung</td>
               <td>$FormattedNote</td>
           </tr>
       <% end_if %>
   </table>

   <table border="0">
       <tbody>
        <tr>
           <td valign="top">
               <h2>Versandadresse:</h2>
               <% control SilvercartShippingAddress %>
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
                           <td>Stra&szlig;e</td><td>$Street</td>
                       </tr>
                       <tr>
                           <td>Hausnummer</td><td>$StreetNumber</td>
                       </tr>
                       <tr>
                           <td>PLZ</td><td>$Postcode</td>
                       </tr>
                       <tr>
                           <td>Stadt</td><td>$City</td>
                       </tr>
                       <tr>
                           <td>Telefonnummer</td><td>
                               <% if Phone %>
                                   {$PhoneAreaCode}/{$Phone}
                               <% else %>
                                   ---
                               <% end_if %>
                           </td>
                       </tr>
                       <tr>
                           <td>Land</td><td>$country.Title</td>
                       </tr>
                   </table>
               <% end_control %>
           </td>
           <td valign="top">
               <h2>Rechnungsadresse:</h2>

               <% control SilvercartInvoiceAddress %>
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
                           <td>Stra&szlig;e</td><td>$Street</td>
                       </tr>
                       <tr>
                           <td>Hausnummer</td><td>$StreetNumber</td>
                       </tr>
                       <tr>
                           <td>PLZ</td><td>$Postcode</td>
                       </tr>
                       <tr>
                           <td>Stadt</td><td>$City</td>
                       </tr>
                       <tr>
                           <td>Telefonnummer</td><td>
                               <% if Phone %>
                                   {$PhoneAreaCode}/{$Phone}
                               <% else %>
                                   ---
                               <% end_if %>
                           </td>
                       </tr>
                       <tr>
                           <td>Land</td><td>$country.Title</td>
                       </tr>
                   </table>
               <% end_control %>
           </td>
       </tr>
   </table>

   <h2>Bestellte Artikel:</h2>
   <table border="0">
       <tbody>
           <tr>
               <th>Artikelname</th>
               <th class="right">Einzelpreis</th>
               <th class="right">MwSt.</th>
               <th class="right">Anzahl</th>
               <th class="right">Preis</th>
           </tr>

           <% control SilvercartOrderPositions(TaxRate > 0) %>
               <tr>
                   <td>$Title</td>
                   <td class="right">$Price.Nice</td>
                   <td class="right">{$TaxRate}%</td>
                   <td class="right">$Quantity</td>
                   <td class="right">$PriceTotal.Nice</td>
               </tr>
           <% end_control %>

           <tr>
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
                   <td></td>
                   <td></td>
               </tr>
               <% end_control %>
           <% end_if %>

           <tr>
               <td>Versandart $CarrierAndShippingMethodTitle</td>
               <td></td>
               <td></td>
               <td></td>
               <td class="right">$HandlingCostShipment.Nice</td>
           </tr>
           <tr>
               <td>Bezahlart $payment.Title</td>
               <td></td>
               <td></td>
               <td></td>
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
                   <td></td>
                   <td></td>
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
               <td><strong>Gesamt</strong></td>
               <td></td>
               <td></td>
               <td></td>
               <td class="right"><strong>$AmountTotal.Nice</strong></td>
           </tr>
       </tbody>
   </table>
<% end_control %>

<p>Mit freundlichem Gru&szlig;,</p>
<p>Ihr SilverCart Webshop Team</p>