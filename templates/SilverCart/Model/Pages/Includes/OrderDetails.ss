<% with $CustomersOrder %>
    <table class="table table-condensed">
        <tbody>
            <tr>
                <td class="text-left nowrap"><%t SilverCart\Model\Pages\Page.ORDER_DATE 'Order date' %></td>
                <td class="text-left full">{$Created.Nice}</td>
            </tr>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('PaymentMethodTitle')}</td>
                <td class="text-left full">{$PaymentMethod.Name}</td>
            </tr>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('OrderStatus')}</td>
                <td class="text-left full">{$OrderStatus.Title}</td>
            </tr>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('PaymentStatus')}</td>
                <td class="text-left full">{$PaymentStatus.Title}</td>
            </tr>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('ShippingFee')}</td>
                <td class="text-left full">{$HandlingCostShipment.Nice}</td>
            </tr>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('OrderAmount')}</td>
                <td class="text-left full">{$AmountTotal.Nice}</td>
            </tr>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('OrderNumber')}</td>
                <td class="text-left full">{$OrderNumber}</td>
            </tr>
            <% if Note %>
            <tr>
                <td class="text-left nowrap">{$fieldLabel('YourNote')}</td>
                <td class="text-left full">{$getFormattedNote}</td>
            </tr>
            <% end_if %>
            <% if $ExpectedDelivery %>
             <tr>
                <td class="text-left nowrap"><strong>{$fieldLabel('ExpectedDelivery')}</strong></td>
                <td class="text-left full">{$ExpectedDeliveryNice}</td>
            </tr>
            <% end_if %>
            <% if $TrackingCode %>
            <tr>
                <td class="text-left nowrap"><strong>{$fieldLabel('Tracking')}</strong></td>
                <td class="text-left full"><a href="{$TrackingLink}" target="blank" title="{$fieldLabel('TrackingLinkLabel')}">{$fieldLabel('TrackingLinkLabel')}</a></td>
            </tr>
            <% end_if %>
            <tr>
                <td class="text-left nowrap"><%t SilverCart\Model\Pages\RevocationFormPage.TITLE 'Revocation' %></td>
                <td class="text-left full"><a class="silvercart-button btn" href="{$CurrentPage.PageByIdentifierCodeLink(SilvercartRevocationFormPage)}?o={$ID}"><%t SilverCart\Forms\RevocationForm.GoTo 'Go to revocation form' %> <i class="icon icon-caret-right"></i></a></td>
            </tr>
        <% with $CurrentPage %>
            <% if $AllowReorder %>
            <tr>
                <td>&nbsp;</td>
                <td class="text-left full">
                    <a class="silvercart-button btn" href="{$ReoderLink}">{$fieldLabel('ButtonReorder')} <i class="icon icon-caret-right"></i></a><br/>
                    <small><i class="icon icon-info-circle"></i> {$fieldLabel('ButtonReorderDesc')}</small>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="text-left full">
                    <a class="silvercart-button btn" href="{$ReoderFullLink}">{$fieldLabel('ButtonReorderFull')} <i class="icon icon-caret-right"></i></a><br/>
                    <small><i class="icon icon-info-circle"></i> {$fieldLabel('ButtonReorderFullDesc')}</small>
                </td>
            </tr>
            <% end_if %>
        <% end_with %>
            {$OrderDetailInformation}
        </tbody>
    </table>
    <div class="row-fluid silvercart-address-equalize">
        <div class="span6">
            <div class="well margin-top">
        <% with $InvoiceAddress %>
            <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
        <% end_with %>
            </div>
        </div>
        <div class="span6">
            <div class="well margin-top">
        <% with $ShippingAddress %>
            <% include SilverCart/Model/Pages/AddressDetailReadOnly %>
        <% end_with %>
            </div>
        </div>
    </div>
    <div class="margin-top">
        {$OrderDetailTable}
    </div>
<% end_with %>
