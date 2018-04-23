<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <div class="row">
        <div class="span6">
            <h4><%t SilverCart\Model\Pages\Page.BILLING_ADDRESS 'Invoice address' %></h4>
            {$BeforeInvoiceAddressContent}
            {$Fields.dataFieldByName(InvoiceAddress).FieldHolder}
            {$AfterInvoiceAddressContent}
        </div>
        <div class="span6">
            <h4><%t SilverCart\Model\Pages\Page.SHIPPING_ADDRESS 'Shipping Address' %></h4>
            <div id="ShippingAddressFields">
                {$Fields.dataFieldByName(ShippingAddress).FieldHolder}
            </div>
            {$Fields.dataFieldByName(InvoiceAddressAsShippingAddress).FieldHolder}
        </div>
    </div>
    <hr>
    {$CustomFormSpecialFields}
    <div class="clearfix">
        <a href="{$CurrentPage.Link}addNewAddress" class="btn btn-small silvercart-trigger-add-address-link js-link"><i class="icon-plus"></i> <%t SilverCart\Model\Pages\AddressHolder.ADD 'Add new address' %></a>
    <% loop $Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
    <% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>