<% if SilvercartAddresses %>
    <h2>$Top.Title</h2>
    <% loop SilvercartAddresses %>
        <% if Odd %>
    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
        <% else %>
        <div class="c50r">
            <div class="subcr">
        <% end_if %>
                <div class="silvercart-address">
                    <div class="subcolumns silvercart-address-top">
                        <div class="c66l">
                            <div class="silvercart-address-field_content">
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
                                    <em><% _t('SilvercartCustomer.REGULARCUSTOMER') %></em><br />
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
                                    $fieldLabel(PhoneShort): $PhoneAreaCode/$Phone
                                <% end_if %>
                            </div>
                        </div>
                        <div class="c33r align_right">
                            <a class="silvercart-icon-button edit32" id="silvercart-edit-shipping-address-id" href="$Top.PageByIdentifierCodeLink(SilvercartAddressPage)$ID" title="<% _t('SilvercartAddressHolder.EDIT','edit') %>">
                                <span class="silvercart-icon-button_content">
                                    &nbsp;
                                </span>
                            </a><% if isLastAddress %><% else %><a class="silvercart-icon-button delete32" id="silvercart-delete-shipping-address-id" href="{$Top.Link}deleteAddress/$ID" title="<% _t('SilvercartAddressHolder.DELETE','Delete') %>">
                                <span class="silvercart-icon-button_content">
                                    &nbsp;
                                </span>
                            </a>
                            <% end_if %>
                        </div>
                    </div>
                    <div class="subcolumns silvercart-address-bottom">
                        <div class="c50l">
                            <div class="silvercart-button-row left clearfix">
                                <% if isInvoiceAddress %>
                                    <p class="silvercart-message">
                                        <strong>
                                            <% _t('SilvercartAddressHolder.DEFAULT_INVOICE','This is your invoice address') %>
                                        </strong>
                                    </p>
                                <% else %>
                                    <div class="silvercart-button left">
                                        <div class="silvercart-button_content">
                                            <a href="{$Top.Link}setInvoiceAddress/$ID"><% _t('SilvercartAddressHolder.SET_AS','Set as') %><br /><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></a>
                                        </div>
                                    </div>
                                <% end_if %>
                            </div>
                        </div>
                        <div class="c50r">
                            <div class="silvercart-button-row right clearfix">
                                <% if isShippingAddress %>
                                    <p class="silvercart-message">
                                        <strong>
                                            <% _t('SilvercartAddressHolder.DEFAULT_SHIPPING','This is your shipping address') %>
                                        </strong>
                                    </p>
                                <% else %>
                                    <div class="silvercart-button right">
                                        <div class="silvercart-button_content">
                                            <a href="{$Top.Link}setShippingAddress/$ID"><% _t('SilvercartAddressHolder.SET_AS','Set as') %><br /><% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></a>
                                        </div>
                                    </div>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>
        <% if Odd %>
            </div>
        </div>
            <% if Last %>
    </div>
            <% end_if %>
        <% else %>
            </div>
        </div>
    </div>
        <% end_if %>
    <% end_loop %>
<% end_if %>
