<form class="yform full" $FormAttributes >
    $CustomHtmlFormMetadata
    <fieldset>
        <legend><% _t('SilvercartAddressHolder.ADD') %></legend>
        <% if EnablePackstation %>
            $CustomHtmlFormFieldByName(IsPackstation,CustomHtmlFormFieldCheckGroup)
        <% end_if %>
        <% if EnableBusinessCustomers %>
            $CustomHtmlFormFieldByName(IsBusinessAccount,CustomHtmlFormFieldCheck)

            <div class="subcolumns">
                <div class="c50l">
                    <div class="subcl">
                        $CustomHtmlFormFieldByName(TaxIdNumber)
                    </div>
                </div>
                <div class="c50r">
                    <div class="subcr">
                        $CustomHtmlFormFieldByName(Company)
                    </div>
                </div>
            </div>
        <% end_if %>
        
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
        <div class="subcolumns absolute-address-data">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Addition)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr"></div>
            </div>
        </div>
        <div class="subcolumns absolute-address-data">
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
    <% if EnablePackstation %>
        <div class="subcolumns packstation-address-data">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(PostNumber)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Packstation)
                </div>
            </div>
        </div>
    <% end_if %>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Postcode)
                 </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(City)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(PhoneAreaCode)
                 </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Phone)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Fax)
                </div>
            </div>
        </div>
    </fieldset>

    $CustomHtmlFormSpecialFields
    
    <div class="type-button clearfix">
        <% control Actions %>
            $Field
            <div class="silvercart-button">
                <div class="silvercart-button_content">
                    <a id="silvercart-add-address-form-cancel-id" href="$Top.CancelLink"><% _t('SilvercartPage.CANCEL') %></a>
                </div>
            </div>
        <% end_control %>
    </div>
</form>
