<form class="yform full" $FormAttributes >
    
      $CustomHtmlFormMetadata

      <fieldset>
        <legend><% _t('SilvercartPage.CONTACT_FORM','contact form') %></legend>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    $CustomHtmlFormFieldByName(Email)
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
        <% if EnableStreet %>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Street,SilvercartStreetWithNumberField)
                </div>
            </div>
        </div>
        <% end_if %>
        <% if EnableCity %>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(City, SilvercartCityWithPostcodeField)
                </div>
            </div>
        </div>
        <% end_if %>
        <% if EnableCountry %>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(SilvercartCountryID)
                </div>
            </div>
        </div>
        <% end_if %>
        <% if EnablePhoneNumber %>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    $CustomHtmlFormFieldByName(Phone)
                </div>
            </div>
        </div>
        <% end_if %>
        $CustomHtmlFormFieldByName(Message)
    </fieldset>

    $CustomHtmlFormSpecialFields

    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>
</form>
