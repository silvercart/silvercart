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
            <div class="row-fluid">
                <div class="span4">
                     $CustomHtmlFormFieldByName(Shipping_IsBusinessAccount,CustomHtmlFormFieldCheck)
                </div>
                <div class="span4">
                    $CustomHtmlFormFieldByName(Shipping_TaxIdNumber)
                </div>
                <div class="span4 last">
                    $CustomHtmlFormFieldByName(Shipping_Company)
                </div>
            </div>  
        <% end_if %>

        <% if EnablePackstation %>
            <div class="clearfix">
                $CustomHtmlFormFieldByName(Shipping_IsPackstation,CustomHtmlFormFieldCheckGroup)
            </div>
        <% end_if %>

        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Shipping_Salutation,CustomHtmlFormFieldSelect)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(Shipping_FirstName)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(Shipping_Surname)
            </div>
        </div>  

        <div class="absolute-address-data">
            <div class="row-fluid">
                <div class="span4">
                    $CustomHtmlFormFieldByName(Shipping_Street)
                </div>
                <div class="span4">
                    $CustomHtmlFormFieldByName(Shipping_StreetNumber)
                </div>
                <div class="span4 last">
                    $CustomHtmlFormFieldByName(Shipping_Addition)
                </div>
            </div>    
        </div>

        <% if EnablePackstation %>
        <div class="packstation-address-data">
            <div class="row-fluid">
                <div class="span4">
                     $CustomHtmlFormFieldByName(Shipping_PostNumber)
                </div>
                <div class="span4">
                    $CustomHtmlFormFieldByName(Shipping_Packstation)
                </div>
                <div class="span4 last">
                </div>
            </div>    
        </div>   
        <% end_if %>

        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Shipping_Postcode)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(Shipping_City)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(Shipping_Country,CustomHtmlFormFieldSelect)
            </div>
        </div>    

        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Shipping_PhoneAreaCode)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(Shipping_Phone)
            </div>
            <div class="span4 last">
            </div>
        </div>    
        
    </div>
    <hr>
    <div class="clearfix">
    <% loop Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
    <% end_loop %>
    </div>
</form>