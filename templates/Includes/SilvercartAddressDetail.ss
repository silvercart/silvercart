 <% if SilvercartAddresses %>
<div class="row">    
    <hr>
    <div class="section-header clearfix">
    <h2>$CurrentPage.Title</h2>
    </div>
    <% loop SilvercartAddresses %>
    <div class="span4 silvercart-address">
        <div class="row silvercart-address-top">
            <div class="span4">
                <div class="silvercart-address-field_content">
                    <div class="btn-group pull-right">
                        <a class="btn btn-small edit32" data-toggle="tooltip" data-placement="top" data-title="<% _t('SilvercartAddressHolder.EDIT','edit') %>" data-original-title="" title="" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.PageByIdentifierCodeLink(SilvercartAddressPage)}edit/{$ID}" title="<% _t('SilvercartAddressHolder.EDIT','edit') %>">
                            <span class="icon-pencil">
                                &nbsp;
                            </span>
                        </a><% if isLastAddress %><% else %><a class="btn btn-small btn-danger" data-toggle="tooltip" data-placement="top" data-title="<% _t('SilvercartAddressHolder.DELETE','Delete') %>" data-original-title="" title="" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/$ID">
                            <span class="icon-trash">
                                &nbsp;
                            </span>
                        </a>
                        <% end_if %>
                    </div>       
                    <% if IsPackstation %>
                    <em><% _t('SilvercartAddress.PACKSTATION_LABEL') %></em><br />
                    <br/>
                    <% else_if isCompanyAddress %>
                    <div class="silvercart-address-company-section">
                        <em><% _t('SilvercartCustomer.BUSINESSCUSTOMER') %></em><br />
                        <% if TaxIdNumber %>$fieldLabel(TaxIdNumber): $TaxIdNumber<br /><% end_if %>
                        <% if Company %>$fieldLabel(Company): $Company<br /><% end_if %>
                        <br/>
                    </div>
                    <% else %>
                    <br />
                    <br/>
                    <% end_if %>
                    $SalutationText $FirstName $Surname<br/>
                    <% if Addition %>
                    $Addition<br/>
                    <% end_if %>
                    <% if IsPackstation %>
                    $PostNumber<br/>
                    $Packstation<br/>
                    <% else %>
                    $Street $StreetNumber<br/>
                    <% end_if %>
                    $Postcode $City<br/>
                    $SilvercartCountry.Title<br/>
                    <% if Phone %>
                    $fieldLabel(PhoneShort): $PhoneAreaCode $Phone
                    <% end_if %>  
                </div>
            </div>
        </div>
        <hr> 
        <div class="silvercart-address-bottom well">
              <% if isInvoiceAddress %>
             <span class="silvercart-message">
                <strong>
                    <% _t('SilvercartAddressHolder.DEFAULT_INVOICE','This is your invoice address') %>
                </strong>
            </span>
              <% end_if %>
             <% if isShippingAddress %>
            <span class="silvercart-message">
                <strong>
                    <% _t('SilvercartAddressHolder.DEFAULT_SHIPPING','This is your shipping address') %>
                </strong>
            </span>
              <% end_if %>
            
            <% if isInvoiceAddress %>
            <% else %>
                <a href="{$CurrentPage.Link}setInvoiceAddress/$ID" class="btn btn-small"><% _t('SilvercartAddressHolder.SET_AS','Set as') %> <% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></a>
            <% end_if %>

            <% if isShippingAddress %>
            <% else %>
                <a href="{$CurrentPage.Link}setShippingAddress/$ID" class="btn btn-small"><% _t('SilvercartAddressHolder.SET_AS','Set as') %> <% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></a>
            <% end_if %>
        </div>
    </div>
    <% end_loop %>
</div>   
<% end_if %>
