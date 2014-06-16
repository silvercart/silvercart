<form name="SupportRevocationForm" id="SupportRevocationForm" method="post">
    <input type="hidden" name="ExistingOrder" value="" id="SupportExistingOrder">
</form>
<form class="yform full" $FormAttributes >
    $CustomHtmlFormMetadata
<% if Customer %>
    <fieldset>
        <legend><% _t('SilvercartRevocationForm.Order') %></legend>
        $CustomHtmlFormFieldByName(ExistingOrder, CustomHtmlFormFieldSelect)
    </fieldset>
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
    <fieldset>
        <legend><% _t('SilvercartRevocationForm.Data') %></legend>
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
        <p><% _t('SilvercartRevocationForm.RevocationDate') %>: <strong>{$CurrentDate}</strong></p>
        $CustomHtmlFormFieldByName(RevocationOrderData)
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(OrderDate)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(OrderNumber)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Email)
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><% _t('SilvercartRevocationForm.NameOfConsumer') %></legend>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(FirstName)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Surname)
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><% _t('SilvercartRevocationForm.AddressOfConsumer') %></legend>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Street)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(StreetNumber)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Addition)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr"></div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Postcode)
                 </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(City)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
                </div>
            </div>
        </div>
    </fieldset>

    $CustomHtmlFormSpecialFields
    
    <div class="type-button clearfix">
        <% loop Actions %>
            $Field
        <% end_loop %>
    </div>
</form>
