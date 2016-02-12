<form name="SupportRevocationForm" id="SupportRevocationForm" method="post">
    <input type="hidden" name="ExistingOrder" value="" id="SupportExistingOrder">
</form>
<form class="form-horizontal grouped" $FormAttributes >
    $CustomHtmlFormMetadata
    <% if Customer %>
        <h4><% _t('SilvercartRevocationForm.Order') %></h4>
        <div class="margin-side">
            $CustomHtmlFormFieldByName(ExistingOrder, CustomHtmlFormFieldSelect)
        </div>
        <script type="text/javascript">
        $(document).ready(function() {
            $('select[name="ExistingOrder"]').live('change', function() {
                console.log($(this).val());
                $('#SupportExistingOrder').val($(this).val());
                $('#SupportRevocationForm').submit();
            });
        });
        </script>
    <% end_if %>
    <h4><% _t('SilvercartRevocationForm.Data') %></h4>
    <div class="margin-side">
        <p><% _t('Silvercart.To') %>:<br/>
            <% with CurrentPage.SilvercartConfig %>
                <i>
                    <strong>{$ShopName}</strong><br/>
                    {$ShopStreet} {$ShopStreetNumber}<br/>
                    {$ShopPostcode} {$ShopCity}<br/>
                    {$ShopCountry.Title}<br/>
                </i>
            <% end_with %>
        </p>
        <p>
            <% _t('SilvercartRevocationForm.RevocationDate') %>: <strong>{$CurrentDate}</strong>
        </p>
        $CustomHtmlFormFieldByName(RevocationOrderData)
        $CustomHtmlFormFieldByName(OrderDate)
        $CustomHtmlFormFieldByName(OrderNumber)
        $CustomHtmlFormFieldByName(Email)
    </div>
    <h4><% _t('SilvercartRevocationForm.NameOfConsumer') %></h4>
    <div class="margin-side">
        $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(FirstName)
        $CustomHtmlFormFieldByName(Surname)
    </div>
    <h4><% _t('SilvercartRevocationForm.AddressOfConsumer') %></h4>
    <div class="margin-side clearfix">
        $CustomHtmlFormFieldByName(Street)
        $CustomHtmlFormFieldByName(StreetNumber)
        $CustomHtmlFormFieldByName(Addition)
        $CustomHtmlFormFieldByName(Postcode)
        $CustomHtmlFormFieldByName(City)
        $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
        $CustomHtmlFormSpecialFields
    <% loop Actions %>
        <button class="btn btn-primary pull-right" type="submit" id="{$ID}" title="$Title" value="$Title">$Title</button> 
    <% end_loop %> 
    </div>
</form>
