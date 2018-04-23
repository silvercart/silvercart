 <% if $Addresses %>
<hr>
<div class="section-header clearfix">
    <h2>$CurrentPage.Title</h2>
</div>
    <% loop $Addresses %>
    <div class="row-fluid">  
    <div class="span4 silvercart-address ">
        <div class="silvercart-address-top">
            <div class="silvercart-address-field_content">
                
                <% if $IsPackstation %>
                <em>{$fieldLabel('PackstationLabel')}</em>
                <br />
                <br/>
                <% else_if $TaxIdNumber || $Company %>
                <div class="silvercart-address-company-section">
                    <% if $isCompanyAddress %><em><%t SilverCart\Model\Customer\Customer.BUSINESSCUSTOMER 'Business customer' %></em><br /><% end_if %>
                    <% if $TaxIdNumber %>{$fieldLabel(TaxIdNumber)}: {$TaxIdNumber}<br /><% end_if %>
                    <% if $Company %>{$Company}<br /><% end_if %>
                    <br/>
                </div>
                <% end_if %>
                <% if $FirstName || $Surname %>
                    {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname}<br/>
                <% end_if %>

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
                {$Country.Title}<br/>
                <% if $Phone %>
                    {$fieldLabel(PhoneShort)}: {$PhoneAreaCode} {$Phone}
                <% end_if %>  
            </div>
        </div>
   
        </div>
        <div class="span8">
        <div class="silvercart-address-bottom wellr">
            <% if $isInvoiceAddress %>
             <span class="silvercart-message">
                <strong><%t SilverCart\Model\Pages\AddressHolder.DEFAULT_INVOICE 'This is your invoice address' %></strong>
            </span>
            <% end_if %>
            <% if $isShippingAddress %>
            <span class="silvercart-message">
                <strong><%t SilverCart\Model\Pages\AddressHolder.DEFAULT_SHIPPING 'This is your shipping address' %></strong>
            </span>
            <% end_if %>
            
            <% if not $isInvoiceAddress && $Member.InvoiceAddress.canEdit %>
                <a href="{$CurrentPage.Link}setInvoiceAddress/$ID" class="btn btn-small"><%t SilverCart\Model\Pages\AddressHolder.SET_AS 'Set as' %> <%t SilverCart\Model\Pages\AddressHolder.INVOICEADDRESS 'invoice address' %></a>
            <% end_if %>

            <% if not $isShippingAddress %>
            <br/><br/><a href="{$CurrentPage.Link}setShippingAddress/$ID" class="btn btn-small"><%t SilverCart\Model\Pages\AddressHolder.SET_AS 'Set as' %> <%t SilverCart\Model\Pages\AddressHolder.SHIPPINGADDRESS 'shipping address' %></a>
            <% end_if %>
            
            <% if $canEdit || $canDelete %>
            <div class="btn-group pull-right">
                <% if $canEdit %>
                <a class="btn btn-small edit32" data-toggle="tooltip" data-placement="top" data-title="<%t SilverCart\Model\Pages\AddressHolder.EDIT 'edit' %>" data-original-title="" title="" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.PageByIdentifierCodeLink(SilvercartAddressHolder)}edit/{$ID}" title="<%t SilverCart\Model\Pages\AddressHolder.EDIT 'edit' %>"><span class="icon-pencil">&nbsp;</span></a>
                <% end_if %>
                <% if not $isLastAddress && $canDelete %>
                <a class="btn btn-small btn-danger" data-toggle="tooltip" data-placement="top" data-title="<%t SilverCart\Model\Pages\AddressHolder.DELETE 'Delete' %>" data-original-title="" title="" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/$ID"><span class="icon-trash">&nbsp;</span></a>
                <% end_if %>
            </div>       
            <% end_if %>
        </div>
    </div>
</div>   
<hr> 
    <% end_loop %>
<% end_if %>
