<form class="yform full" $FormAttributes >
    <fieldset>
        <legend>Artikel</legend>
        <table class="cartSummary">
            <thead>
                <tr>
                    <th>Artikelname</th>
                    <th>Einzelpreis</th>
                    <th>MwSt.</th>
                    <th class="right">Anzahl</th>
                    <th class="right">Preis </th>
                </tr>
            </thead>

            <tbody>
                <% control CurrentMember %>
                    <% control shoppingCart %>
                        <% control positions %>
                            <tr<% if Last %> class="separator"<% end_if %>>
                                <td>$article.Title</td>
                                <td class="right">$article.Price.Nice</td>
                                <td class="right">{$article.tax.Rate}%</td>
                                <td class="right">$Quantity</td>
                                <td class="right">$Price.Nice</td>
                            </tr>
                        <% end_control %>

                        <% control registeredModules %>
                            <% if TaxableShoppingCartPositions %>
                                <% control TaxableShoppingCartPositions %>
                                    <tr>
                                        <td>$Name</td>
                                        <td class="right">$PriceFormatted</td>
                                        <td class="right">$Tax.Title</td>
                                        <td class="right">$Quantity</td>
                                        <td class="right">$PriceTotalFormatted</td>
                                    </tr>
                                <% end_control %>
                            <% end_if %>
                        <% end_control %>

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Zwischensumme</strong></td>
                            <td class="right" id="Sum"><strong>$TaxableAmountGrossWithoutFees.Nice</strong></td>
                            <td></td>
                            <td></td>
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

                        <% control Top.controller %>
                            <tr">
                                <td>Versandart: $CarrierAndShippingMethodTitle</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="right">$HandlingCostShipment.Nice</td>
                            </tr>
                            <tr>
                                <td>Bezahlart: $payment.Title</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="right">$HandlingCostPayment.Nice</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>Zwischensumme</strong></td>
                                <td class="right" id="Sum"><strong>$TaxableAmountGrossWithFees.Nice</strong></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <% end_control %>

                        <% control registeredModules %>
                            <% if NonTaxableShoppingCartPositions %>
                                <% control NonTaxableShoppingCartPositions %>
                                    <tr>
                                        <td>$Name</td>
                                        <td class="right">$PriceFormatted</td>
                                        <td></td>
                                        <td class="right">$Quantity</td>
                                        <td class="right">$PriceTotalFormatted</td>
                                    </tr>
                                <% end_control %>
                            <% end_if %>
                        <% end_control %>

                    <% end_control %>
                <% end_control %>

                <% control controller %>
                    <tr>
                        <td><strong>Gesamtbetrag</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right"><strong>$AmountTotal.Nice</strong></td>
                    </tr>
                <% end_control %>
            </tbody>

        </table>
    </fieldset>

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
                <fieldset>
                    <legend>Versandadresse</legend>
                    <% control AddressData %>
                        <% control shippingAddress %>
                         <% include AddressTable %>
                        <% end_control %>
                    <% end_control %>
                </fieldset>
            </div>
        </div>

        <div class="c50r">
            <div class="subcr">
                <fieldset>
                    <legend>Rechnungsadresse</legend>
                    <% control AddressData %>
                        <% control invoiceAddress %>
                            <% include AddressTable %>
                        <% end_control %>
                    <% end_control %>
                </fieldset>
            </div>
        </div>
    </div>

      <fieldset>
          <legend>Bemerkung</legend>
          $CustomHtmlFormFieldByName(Note)
    </fieldset>
    <fieldset>
        <legend>AGB und Datenschutz</legend>
        $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,HasAcceptedTermsAndConditionsFieldCheck)
        $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,HasAcceptedRevocationInstructionFieldCheck)
        $CustomHtmlFormFieldByName(SubscribedToNewsletter,CustomHtmlFormFieldCheck)
    </fieldset>
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>

    $dataFieldByName(SecurityID)
</form>