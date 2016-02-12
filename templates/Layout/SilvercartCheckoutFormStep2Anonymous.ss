<form class="form-horizontal grouped" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <h4><% _t('SilvercartPage.EMAIL_ADDRESS') %></h4>
    <div class="margin-side">
        $CustomHtmlFormFieldByName(Email)
    </div>
<% if UseMinimumAgeToOrder %>
    <h4><% _t('SilvercartPage.BIRTHDAY') %></h4>
    <div class="margin-side">
        <div class="row">
            <div class="span3">
                $CustomHtmlFormFieldByName(BirthdayDay,CustomHtmlFormFieldSelect)
            </div>
            <div class="span3">
                $CustomHtmlFormFieldByName(BirthdayMonth,CustomHtmlFormFieldSelect)
            </div>
            <div class="span3">
                $CustomHtmlFormFieldByName(BirthdayYear)
            </div>
        </div>
    </div>
<% end_if %>
    <h4><% _t('SilvercartPage.BILLING_ADDRESS') %></h4>
    <div class="margin-side">
    <% if EnableBusinessCustomers %>
        $CustomHtmlFormFieldByName(Invoice_IsBusinessAccount,CustomHtmlFormFieldCheck)
        $CustomHtmlFormFieldByName(Invoice_TaxIdNumber)
        $CustomHtmlFormFieldByName(Invoice_Company)
        <hr>
    <% end_if %>
        $CustomHtmlFormFieldByName(Invoice_Salutation,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(Invoice_FirstName)
        $CustomHtmlFormFieldByName(Invoice_Surname)
        
        $CustomHtmlFormFieldByName(Invoice_Street)
        $CustomHtmlFormFieldByName(Invoice_StreetNumber)
        $CustomHtmlFormFieldByName(Invoice_Addition)
        
        $CustomHtmlFormFieldByName(Invoice_Postcode)
        $CustomHtmlFormFieldByName(Invoice_City)
        $CustomHtmlFormFieldByName(Invoice_Country,CustomHtmlFormFieldSelect)
        
        $CustomHtmlFormFieldByName(Invoice_PhoneAreaCode)
        $CustomHtmlFormFieldByName(Invoice_Phone)
    </div>

    <h4><% _t('SilvercartPage.SHIPPING_ADDRESS') %></h4>
    <div class="margin-side clearfix">
        <div class="clearfix">
            $CustomHtmlFormFieldByName(InvoiceAddressAsShippingAddress, CustomHtmlFormFieldCheck)
        </div>

        <div id="ShippingAddressFields" class="clearfix">

        <% if EnableBusinessCustomers %>
            $CustomHtmlFormFieldByName(Shipping_IsBusinessAccount,CustomHtmlFormFieldCheck)
            $CustomHtmlFormFieldByName(Shipping_TaxIdNumber)
            $CustomHtmlFormFieldByName(Shipping_Company)
        <% end_if %>

        <% if EnablePackstation %>
        <div class="clearfix">
            $CustomHtmlFormFieldByName(Shipping_IsPackstation,CustomHtmlFormFieldCheckGroup)
        </div>
        <% end_if %>
        
            $CustomHtmlFormFieldByName(Shipping_Salutation,CustomHtmlFormFieldSelect)
            $CustomHtmlFormFieldByName(Shipping_FirstName)
            $CustomHtmlFormFieldByName(Shipping_Surname)
            <div class="absolute-address-data">
                $CustomHtmlFormFieldByName(Shipping_Street)
                $CustomHtmlFormFieldByName(Shipping_StreetNumber)
                $CustomHtmlFormFieldByName(Shipping_Addition)
            </div>
        <% if EnablePackstation %>
            <div class="packstation-address-data">
                $CustomHtmlFormFieldByName(Shipping_PostNumber)
                $CustomHtmlFormFieldByName(Shipping_Packstation)
            </div>
        <% end_if %>
        $CustomHtmlFormFieldByName(Shipping_Postcode)
        $CustomHtmlFormFieldByName(Shipping_City)
        $CustomHtmlFormFieldByName(Shipping_Country,CustomHtmlFormFieldSelect)

        $CustomHtmlFormFieldByName(Shipping_PhoneAreaCode)
        $CustomHtmlFormFieldByName(Shipping_Phone)
        </div>
    </div>
    <hr>
    <div class="margin-side clearfix">
    <% loop Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
    <% end_loop %>
    </div>
</form>