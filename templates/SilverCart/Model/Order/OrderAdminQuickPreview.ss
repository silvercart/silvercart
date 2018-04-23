<h3>{$singular_name}</h3>
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
        <td colspan="2"><a href="admin/silvercart-orders/SilverCart-Model-Order-Order/EditForm/field/SilverCart-Model-Order-Order/item/{$ID}/edit"><%t SilverCart\Model\Pages\Page.SHOW_DETAILS 'Show details' %> &gt;&gt;</a></td>
    </tr>
</table>
