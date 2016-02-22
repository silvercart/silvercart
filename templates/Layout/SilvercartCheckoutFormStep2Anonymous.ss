<form class="form-vertical grouped" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <h4><% _t('SilvercartPage.EMAIL_ADDRESS') %></h4>
    
    $CustomHtmlFormFieldByName(Email)
    
    <% if UseMinimumAgeToOrder %>
    <h4><% _t('SilvercartPage.BIRTHDAY') %></h4>
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
    <% end_if %>
    
    <h4><% _t('SilvercartPage.BILLING_ADDRESS') %></h4>
    <% if EnableBusinessCustomers %>
    <div class="row-fluid">
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_IsBusinessAccount,CustomHtmlFormFieldCheck)
        </div>
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_TaxIdNumber)
        </div>
        <div class="span4 last">
            $CustomHtmlFormFieldByName(Invoice_Company)
        </div>
    </div>    
    <hr>
    <% end_if %>

    <div class="row-fluid">
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_Salutation,CustomHtmlFormFieldSelect)
        </div>
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_FirstName)
        </div>
        <div class="span4 last">
            $CustomHtmlFormFieldByName(Invoice_Surname)
        </div>
    </div>    

    <div class="row-fluid">
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_Street)
        </div>
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_StreetNumber)
        </div>
        <div class="span4 last">
            $CustomHtmlFormFieldByName(Invoice_Addition)
        </div>
    </div>  

    <div class="row-fluid">
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_Postcode)
        </div>
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_City)
        </div>
        <div class="span4 last">
            $CustomHtmlFormFieldByName(Invoice_Country,CustomHtmlFormFieldSelect)
        </div>
    </div>  


    <div class="row-fluid">
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_PhoneAreaCode)
        </div>
        <div class="span4">
            $CustomHtmlFormFieldByName(Invoice_Phone)
        </div>
        <div class="span4 last">
        </div>
    </div>  

    <h4><% _t('SilvercartPage.SHIPPING_ADDRESS') %></h4>
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
    <hr>
    <div class="clearfix">
        <% loop Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
        <% end_loop %>
    </div>
</form>