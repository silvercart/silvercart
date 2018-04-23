<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>{$singular_name} $OrderNumber | $Created.Nice</title>
    </head>
    <body>

        <table>
            <colgroup>
                <col width="40%"></col>
                <col width="30%"></col>
                <col width="30%"></col>
            </colgroup>
            <tr>
                <td valign="top">
                <% if InvoiceAddressEqualsShippingAddress %>
                    <h2><%t SilverCart\Model\Pages\Page.SHIPPING_AND_BILLING 'Shipping and invoice address' %>:</h2>
                <% else %>
                    <h2>{$InvoiceAddress.fieldLabel('InvoiceAddress')}:</h2>
                <% end_if %>
                    <% with $InvoiceAddress %>
                    <table>
                        <% if $TaxIdNumber %>
                            <tr>
                                <td>{$fieldLabel('TaxIdNumber')}</td>
                                <td>$TaxIdNumber</td>
                            </tr>
                        <% end_if %>
                        <% if Company %>
                            <tr>
                                <td>{$fieldLabel('Company')}</td>
                                <td>$Company</td>
                            </tr>
                        <% end_if %>
                        <tr>
                            <td>{$fieldLabel('FirstName')}</td>
                            <td>$FirstName</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Surname')}</td>
                            <td>$Surname</td>
                        </tr>
                        <% if Addition %>
                        <tr>
                            <td>{$fieldLabel('Addition')}</td>
                            <td>$Addition</td>
                        </tr>
                        <% end_if %>
                        <tr>
                            <td>{$fieldLabel('Street')}</td>
                            <td>$Street</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('StreetNumber')}</td>
                            <td>$StreetNumber</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Postcode')}</td>
                            <td>$Postcode</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('City')}</td>
                            <td>$City</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Phone')}</td>
                            <td><% if Phone %>{$PhoneAreaCode} {$Phone}<% else %>---<% end_if %></td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Fax')}</td>
                            <td>$Fax</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Country')}</td>
                            <td>$Country.Title</td>
                        </tr>
                    </table>
                    <% end_with %>
                </td>
                <td valign="top">
                <% if not $InvoiceAddressEqualsShippingAddress %>
                    <h2><%t SilverCart\Model\Pages\Page.SHIPPING_ADDRESS 'Shipping address' %>:</h2>
                    <% with $ShippingAddress %>
                    <table>
                        <% if TaxIdNumber %>
                            <tr>
                                <td>{$fieldLabel('TaxIdNumber')}</td>
                                <td>$TaxIdNumber</td>
                            </tr>
                        <% end_if %>
                        <% if Company %>
                            <tr>
                                <td>{$fieldLabel('Company')}</td>
                                <td>$Company</td>
                            </tr>
                        <% end_if %>
                        <tr>
                            <td>{$fieldLabel('FirstName')}</td>
                            <td>$FirstName</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Surname')}</td>
                            <td>$Surname</td>
                        </tr>
                        <% if Addition %>
                        <tr>
                            <td>{$fieldLabel('Addition')}</td>
                            <td>$Addition</td>
                        </tr>
                        <% end_if %>
                        <% if $IsPackstation %>
                        <tr>
                            <td>{$fieldLabel('PostNumberPlain')}</td>
                            <td>$PostNumber</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('PackstationPlain')}</td>
                            <td>$Packstation</td>
                        </tr>
                        <% else %>
                        <tr>
                            <td>{$fieldLabel('Street')}</td>
                            <td>$Street</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('StreetNumber')}</td>
                            <td>$StreetNumber</td>
                        </tr>
                        <% end_if %>
                        <tr>
                            <td>{$fieldLabel('Postcode')}</td>
                            <td>$Postcode</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('City')}</td>
                            <td>$City</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Phone')}</td>
                            <td><% if Phone %>{$PhoneAreaCode} {$Phone}<% else %>---<% end_if %></td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Fax')}</td>
                            <td>$Fax</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('Country')}</td>
                            <td>$Country.Title</td>
                        </tr>
                    </table>
                    <% end_with %>
                <% end_if %>
                </td>
                <td valign="top">
                    <h2><%t SilverCart\Model\Pages\OrderDetailPage.TITLE 'Order details' %>:</h2>

                    <table>
                        <tr>
                            <td>{$fieldLabel('Email')}</td>
                            <td>$CustomersEmail</td>
                        </tr>
                        <tr>
                            <td><%t SilverCart\Model\Pages\Page.ORDER_DATE 'Order date' %></td>
                            <td>{$Created.Nice}</td>
                        </tr>
                        <tr>
                            <td><%t SilverCart\Model\Order\NumberRange.ORDERNUMBER 'Ordernumber' %></td>
                            <td>{$OrderNumber}</td>
                        </tr>
                        <tr>
                            <td>{$fieldLabel('OrderStatus')}</td>
                            <td>$OrderStatus.Title</td>
                        </tr>
                        <% if $Note %>
                            <tr>
                                <td>{$fieldLabel('YourNote')}</td>
                                <td>$FormattedNote</td>
                            </tr>
                        <% end_if %>
                    </table>
                </td>
            </tr>
        </table>

        <h2><%t SilverCart\Model\Pages\Page.ORDERED_PRODUCTS 'Ordered products' %>:</h2>
        {$OrderDetailTable}
    
        <br/>
        <br style="page-break-after: always;"/>
        <button onclick="javascript:window.print();">{$fieldLabel('Print')}</button>
    </body>
</html>