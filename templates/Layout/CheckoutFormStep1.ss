<form class="yform" $FormAttributes >

      $CustomHtmlFormMetadata
      $CustomHtmlFormErrorMessages
      <fieldset>
          <legend><% _t('Page.EMAIL_ADDRESS','email address') %></legend>
          <div class="subcolumns">
              $CustomHtmlFormFieldByName(Email)
          </div>
    </fieldset>

      <fieldset>
        <legend><% _t('Page.BILLING_ADDRESS','billing address') %></legend>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_Salutation,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_FirstName)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Invoice_Surname)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_Street)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_StreetNumber)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Invoice_Addition)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_Postcode)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_City)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Invoice_Country,CustomHtmlFormFieldSelect)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_PhoneAreaCode)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_Phone)
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><% _t('Page.SHIPPING_ADDRESS','shipping address') %></legend>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Shipping_Salutation,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Shipping_FirstName)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Shipping_Surname)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Shipping_Street)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Shipping_StreetNumber)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Shipping_Addition)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Shipping_Postcode)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Shipping_City)
                </div>
            </div>
            <div class="c33r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Shipping_Country,CustomHtmlFormFieldSelect)
                </div>
            </div>
        </div>
        <div class="subcolumns">
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_PhoneAreaCode)
                </div>
            </div>
            <div class="c33l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Invoice_Phone)
                </div>
            </div>
        </div>
    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>

    $dataFieldByName(SecurityID)
</form>