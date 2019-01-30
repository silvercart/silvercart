 <% if $SilvercartAddresses %>
<hr>
<div class="section-header clearfix">
    <h2>{$CurrentPage.Title}</h2>
</div>
    <% loop $SilvercartAddresses %>
    <div class="row-fluid">
    <div class="span4 silvercart-address ">
        <div class="silvercart-address-top">
            <div class="silvercart-address-field_content">
                
            <% if $IsPackstation %>
                <em><% _t('SilvercartAddress.PACKSTATION_LABEL') %></em>
                <br/>
            <% else_if $TaxIdNumber || $Company %>
                <div class="silvercart-address-company-section">
                    <% if $isCompanyAddress %><em><%t SilvercartCustomer.BUSINESSCUSTOMER %></em><br /><% end_if %>
                    <% if $TaxIdNumber %>{$fieldLabel(TaxIdNumber)}: {$TaxIdNumber}<br /><% end_if %>
                    <% if $Company %>{$Company}<br /><% end_if %>
                </div>
            <% end_if %>
                {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname}<br/>
            <% if $Addition %>
                {$Addition}<br/>
            <% end_if %>
            <% if $IsPackstation %>
                {$PostNumber}<br/>
                {$Packstation}<br/>
            <% else %>
                {$Street} {$StreetNumber}<br/>
            <% end_if %>
                {$Postcode} {$City}<br/>
                {$SilvercartCountry.Title}<br/>
            <% if $Phone %>
                {$fieldLabel(PhoneShort)}: {$PhoneAreaCode} {$Phone}
            <% end_if %>
            </div>
        </div>
   
        </div>
        <div class="span8">
        <div class="silvercart-address-bottom wellr">
        <% if $isInvoiceAddress %>
            <span class="silvercart-message"><strong><%t SilvercartAddressHolder.DEFAULT_INVOICE 'This is your invoice address' %></strong></span>
        <% end_if %>
        <% if $isShippingAddress %>
            <span class="silvercart-message"><strong><%t SilvercartAddressHolder.DEFAULT_SHIPPING 'This is your shipping address' %></strong></span>
        <% end_if %>
        <% if $canEdit %>
            <% if not $isInvoiceAddress && $Member.SilvercartInvoiceAddress.canEdit %>
            <a href="{$CurrentPage.Link}setInvoiceAddress/$ID" class="btn btn-small"><%t SilvercartAddressHolder.SET_AS 'Set as' %> <%t SilvercartAddressHolder.INVOICEADDRESS 'invoice address' %></a>
            <% end_if %>
            <% if not $isShippingAddress %>
            <br/><br/><a href="{$CurrentPage.Link}setShippingAddress/$ID" class="btn btn-small"><%t SilvercartAddressHolder.SET_AS 'Set as' %> <%t SilvercartAddressHolder.SHIPPINGADDRESS 'shipping address' %></a>
            <% end_if %>
        <% end_if %>
        <% if $canEdit || $canDelete %>
            <div class="btn-group pull-right">
            <% if $canEdit %>
                <a class="btn btn-small" data-toggle="tooltip" data-placement="top" title="<%t SilvercartAddressHolder.EDIT 'edit' %>" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.PageByIdentifierCodeLink(SilvercartAddressPage)}edit/{$ID}" title="<%t SilvercartAddressHolder.EDIT 'edit' %>"><span class="icon-pencil">&nbsp;</span></a>
            <% end_if %>
            <% if not $isLastAddress && $canDelete %>
                <a class="btn btn-small btn-danger" data-toggle="tooltip" data-placement="top" title="<%t SilvercartAddressHolder.DELETE 'Delete' %>" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/{$ID}"><span class="icon-trash">&nbsp;</span></a>
            <% end_if %>
            </div>
        <% end_if %>
        </div>
    </div>
</div>
<hr>
    <% end_loop %>
<% end_if %>
