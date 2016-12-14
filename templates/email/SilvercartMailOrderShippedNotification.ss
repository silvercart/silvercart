<style type="text/css">
    h1 { font-size: 14px; }
    h2 { font-size: 12px; }
    body, table td { font-size: 10px; }
    table { width: auto; border-collapse:collapse; margin-bottom:0.5em; border-top:0px; border-bottom:0px; }
    table caption { font-variant:small-caps; }
    table.full { width:100%; }
    table.fixed { table-layout:fixed; }

    th,td { padding: 0.2em 0.5em; }
    thead th { font-size: 10px; border-bottom:1px #666 solid; }
    tbody th { font-size: 10px; background:#e0e0e0; color:#666; border-bottom:1px solid #fff; text-align:left; }
    tbody th[scope="row"], tbody th.sub { background:#f0f0f0; }

    tbody th { border-bottom:1px solid #fff; text-align:left; }
    tbody td { border-bottom:1px solid #eee; }

    tfoot td {border-top: 1px #666 solid; }
</style>
<h1><% _t('SilvercartShopEmail.ORDER_SHIPPED_NOTIFICATION') %></h1>

<p><% _t('SilvercartShopEmail.HELLO', 'Hello') %> {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname},</p>
<p><% _t('SilvercartShopEmail.ORDER_SHIPPED_MESSAGE', 'Your order has been shipped.') %></p>

<% with SilvercartOrder %>
    <table>
        <colgroup>
            <col width="60%"></col>
            <col width="20%"></col>
            <col width="20%"></col>
        </colgroup>
        <tr>
            <td valign="top">
                <h2><% _t('SilvercartOrderDetailPage.TITLE') %>:</h2>

                <table>
                    <tr>
                        <td><% _t('SilvercartAddress.EMAIL') %></td>
                        <td>$CustomersEmail</td>
                    </tr>
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
                <% if trackingCodeNVE %>
                    <tr>
                        <td><% _t('SilvercartShipmentDhlOrder.TRACKING_CODE_NVE_FOR_TRACKING') %></td>
                        <td>$trackingCodeNVE</td>
                    </tr>
                <% end_if %>
                </table>
            </td>
            <td valign="top">
                <h2><% _t('SilvercartPage.SHIPPING_ADDRESS') %>:</h2>
                $ShippingAddressTable
            </td>
            <td valign="top">
                <h2><% _t('SilvercartInvoiceAddress.SINGULARNAME') %>:</h2>
                $InvoiceAddressTable
            </td>
        </tr>
    </table>

    <h2><% _t('SilvercartPage.ORDERED_PRODUCTS') %>:</h2>
    $OrderDetailTable
<% end_with %>

<p><% _t('SilvercartShopEmail.REGARDS', 'Best regards') %>,</p>
<p><% _t('SilvercartShopEmail.YOUR_TEAM', 'Your SilverCart ecommerce team') %></p>
