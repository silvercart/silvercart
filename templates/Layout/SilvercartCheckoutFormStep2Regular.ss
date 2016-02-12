<form class="form-horizontal grouped" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <h4><% _t('SilvercartPage.BILLING_ADDRESS') %> 
        <!--<% loop Actions %><button class="btn btn-small btn-primary btn-checkout-proceed-top pull-right" type="submit" id="{$ID}-Top" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button><% end_loop %>-->
    </h4>
    <div class="margin-side">
        $CustomHtmlFormFieldByName(InvoiceAddress,SilvercartCustomHtmlFormFieldAddress)
        <a href="{$CurrentPage.Link}addNewAddress" class="btn btn-small silvercart-trigger-add-address-link js-link"><i class="icon-plus"></i> <% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
        <br/>
        <br/>
    </div>
    <h4><% _t('SilvercartPage.SHIPPING_ADDRESS') %></h4>
    <div class="margin-side">
        $CustomHtmlFormFieldByName(InvoiceAddressAsShippingAddress, CustomHtmlFormFieldCheck)
        <div id="ShippingAddressFields">
            $CustomHtmlFormFieldByName(ShippingAddress,SilvercartCustomHtmlFormFieldAddress)
            <div class="silvercart-button m25l">
                <div class="silvercart-button_content">
                    <a href="{$CurrentPage.Link}addNewAddress" class="btn btn-small silvercart-trigger-add-address-link js-link"><i class="icon-plus"></i> <% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="margin-side clearfix">
    <% loop Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
    <% end_loop %>
    </div>
</form>
<% require javascript(silvercart/script/SilvercartAddressHolder.js) %>