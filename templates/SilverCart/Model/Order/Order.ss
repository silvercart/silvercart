<div class="clearfix justify-content-center w-100 d-lg-flex">
<% if $InvoiceAddressEqualsShippingAddress %>
    {$ShippingAddress.render($fieldLabel('ShippingAndInvoiceAddress'))}
<% else %>
    {$ShippingAddress.render($fieldLabel('ShippingAddress'))}
    {$InvoiceAddress.render($fieldLabel('InvoiceAddress'))}
<% end_if %>
    <div class="d-inline-block bg-white text-secondary font-italic font-weight-light letter-spacing-1 px-3 py-2 mx-0 mx-md-1 my-1 border w-lg-auto w-100">
        <strong class="font-big-4"><%t SilverCart\Model\Pages\OrderHolder.OrderDetails 'Order details' %></strong><br/>
        <div class="text-nowrap">
            <table class="table table-sm mt-1 mb-0 mx--3 mx-md-0 d-sm-block d-md-inline border-right">
                <tr>
                    <td class="p-1">{$fieldLabel('Email')}</td>
                    <td class="p-1 font-weight-bold text-right"><% if $CustomersEmail %>{$CustomersEmail}<% else %>---<% end_if %></td>
                </tr>
                <tr>
                    <td class="p-1"><%t SilverCart\Model\Pages\Page.ORDER_DATE 'Order date' %></td>
                    <td class="p-1 text-right">{$Created.Nice}</td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('OrderNumber')}</td>
                    <td class="p-1 font-weight-bold text-right">{$OrderNumber}</td>
                </tr>
        <% if $OrderDetailInformationAfterOrderNumber %>
            <% loop $OrderDetailInformationAfterOrderNumber %>
                <tr>
                    <td class="p-1">{$Title}</td>
                    <td class="p-1 text-{$Alignment} <% if $Highlight %>font-weight-bold<% end_if %>">{$Value}</td>
                </tr>
            <% end_loop %>
        <% end_if %>
                <tr>
                    <td class="p-1">{$fieldLabel('OrderStatus')}</td>
                    <td class="p-1 font-weight-bold text-right">{$OrderStatus.Title}</td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('PaymentStatus')}</td>
                    <td class="p-1 font-weight-bold text-right">{$PaymentStatus.Title}</td>
                </tr>
            </table>
            <table class="table table-sm mt-1 mb-0 mx--3 mx-md-0 d-sm-block d-md-inline">
                <tr>
                    <td class="p-1">{$Member.fieldLabel('CustomerNumber')}</td>
                    <td class="p-1 font-weight-bold text-right"><% if $Member.CustomerNumber %>{$Member.CustomerNumber}<% else %>---<% end_if %></td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('ShippingMethod')}</td>
                    <td class="p-1 text-right">{$ShippingMethod.TitleWithCarrier}</td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('HandlingCostShipment')}</td>
                    <td class="p-1 text-right">{$HandlingCostShipment.Nice}</td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('PaymentMethod')}</td>
                    <td class="p-1 text-right">{$PaymentMethod.Title}</td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('HandlingCostPayment')}</td>
                    <td class="p-1 text-right">{$HandlingCostPayment.Nice}</td>
                </tr>
                <tr>
                    <td class="p-1">{$fieldLabel('AmountTotal')}</td>
                    <td class="p-1 font-weight-bold text-right">{$AmountTotal.Nice}</td>
                </tr>
            </table>
        </div>
    </div>
<% if $Note %>
    <div class="d-inline-block bg-white text-secondary font-italic font-weight-light letter-spacing-1 px-3 py-2 mx-0 mx-md-1 my-1 border w-lg-auto w-100">
        <strong class="font-big-4">{$fieldLabel('Note')}</strong><br/>
        <blockquote>{$FormattedNote}</blockquote>
    </div>
<% end_if %>
</div>
<div class="clearfix">
    {$OrderDetailTable}
</div>