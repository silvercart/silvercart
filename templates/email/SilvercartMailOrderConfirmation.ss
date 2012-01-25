<style type="text/css">
    body, table td { font-size: 12px; }
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
<h1><% _t('SilvercartOrderConfirmationPage.ORDERCONFIRMATION') %></h1>

<p><% _t('SilvercartShopEmail.HELLO', 'Hello') %> $Salutation $FirstName $Surname,</p>
<p><% _t('SilvercartShopEmail.ORDER_ARRIVED', 'We confirm your order, many thanks.') %></p>
<p><% _t('SilvercartOrderDetailPage.TITLE') %>:</p>

<% control SilvercartOrder %>
    <table>
        <tr>
            <td><% _t('SilvercartPage.ORDER_DATE') %></td>
            <td>$Created.Nice</td>
        </tr>
        <tr>
            <td><% _t('SilvercartNumberRange.ORDERNUMBER') %></td>
            <td>$OrderNumber</td>
        </tr>
        <tr>
            <td><% _t('SilvercartOrder.STATUS') %></td>
            <td>$SilvercartOrderStatus.Title</td>
        </tr>
        <% if Note %>
            <tr>
                <td><% _t('SilvercartOrder.YOUR_REMARK') %></td>
                <td>$FormattedNote</td>
            </tr>
        <% end_if %>
    </table>

    <table>
        <tbody>
            <tr>
                <td>
                    <h2><% _t('SilvercartPage.SHIPPING_ADDRESS') %>:</h2>
                    <% control SilvercartShippingAddress %>
                    <table>
                        <% if isCompanyAddress %>
                            <tr>
                                <td><% _t('SilvercartAddress.TAXIDNUMBER') %></td>
                                <td>$TaxIdNumber</td>
                            </tr>
                            <tr>
                                <td><% _t('SilvercartAddress.COMPANY') %></td>
                                <td>$Company</td>
                            </tr>
                        <% end_if %>
                        <tr>
                            <td><% _t('SilvercartAddress.FIRSTNAME') %></td>
                            <td>$FirstName</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.SURNAME') %></td>
                            <td>$Surname</td>
                        </tr>
                        <% if Addition %>
                        <tr>
                            <td><% _t('SilvercartAddress.ADDITION') %></td>
                            <td>$Addition</td>
                        </tr>
                        <% end_if %>
                        <tr>
                            <td><% _t('SilvercartAddress.STREET') %></td>
                            <td>$Street</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.STREETNUMBER') %></td>
                            <td>$StreetNumber</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.POSTCODE') %></td>
                            <td>$Postcode</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.CITY') %></td>
                            <td>$City</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.PHONE') %></td>
                            <td><% if Phone %>{$PhoneAreaCode}/{$Phone}<% else %>---<% end_if %></td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.FAX') %></td>
                            <td>$Fax</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartCountry.SINGULARNAME') %></td>
                            <td>$SilvercartCountry.Title</td>
                        </tr>
                    </table>
                    <% end_control %>
                </td>
                <td>
                    <h2><% _t('SilvercartInvoiceAddress.SINGULARNAME') %>:</h2>
                    <% control SilvercartInvoiceAddress %>
                    <table>
                        <% if isCompanyAddress %>
                            <tr>
                                <td><% _t('SilvercartAddress.TAXIDNUMBER') %></td>
                                <td>$TaxIdNumber</td>
                            </tr>
                            <tr>
                                <td><% _t('SilvercartAddress.COMPANY') %></td>
                                <td>$Company</td>
                            </tr>
                        <% end_if %>
                        <tr>
                            <td><% _t('SilvercartAddress.FIRSTNAME') %></td>
                            <td>$FirstName</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.SURNAME') %></td>
                            <td>$Surname</td>
                        </tr>
                        <% if Addition %>
                        <tr>
                            <td><% _t('SilvercartAddress.ADDITION') %></td>
                            <td>$Addition</td>
                        </tr>
                        <% end_if %>
                        <tr>
                            <td><% _t('SilvercartAddress.STREET') %></td>
                            <td>$Street</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.STREETNUMBER') %></td>
                            <td>$StreetNumber</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.POSTCODE') %></td>
                            <td>$Postcode</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.CITY') %></td>
                            <td>$City</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.PHONE') %></td>
                            <td><% if Phone %>{$PhoneAreaCode}/{$Phone}<% else %>---<% end_if %></td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartAddress.FAX') %></td>
                            <td>$Fax</td>
                        </tr>
                        <tr>
                            <td><% _t('SilvercartCountry.SINGULARNAME') %></td>
                            <td>$SilvercartCountry.Title</td>
                        </tr>
                    </table>
                    <% end_control %>
                </td>
            </tr>
        </tbody>
    </table>

    <h2><% _t('SilvercartPage.ORDERED_PRODUCTS') %>:</h2>
    $OrderMailTemplate
<% end_control %>

<p><% _t('SilvercartShopEmail.REGARDS', 'Best regards') %>,</p>
<p><% _t('SilvercartShopEmail.YOUR_TEAM', 'Your SilverCart ecommerce team') %></p>
