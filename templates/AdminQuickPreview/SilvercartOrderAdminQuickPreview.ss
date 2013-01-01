<h3>$singular_name</h3>
<table class="admin-quick-preview">
    <tr>
        <th>{$fieldLabel(OrderNumber)}:</th>
        <td>{$OrderNumber}</td>
    </tr>
    <tr>
        <th>{$fieldLabel(Created)}:</th>
        <td>{$CreatedNice}</td>
    </tr>
    <tr>
        <th>{$fieldLabel(ValueOfGoods)}:</th>
        <td>{$TaxableAmountWithoutFeesNice}</td>
    </tr>
    <tr>
        <th>{$fieldLabel(AmountTotal)}:</th>
        <td>{$AmountTotalNice}</td>
    </tr>
    <tr>
        <td colspan="2"><a href="admin/silvercart-orders/SilvercartOrder/{$ID}/edit"><% _t('SilvercartPage.SHOW_DETAILS') %> &gt;&gt;</a></td>
    </tr>
</table>
