<form class="yform" $FormAttributes >

	$CustomHtmlFormMetadata

	<fieldset>
		<legend><% _t('SilvercartPage.ADDRESS', 'address') %></legend>
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
        <div class="subcolumns">
            $CustomHtmlFormFieldByName(Addition)
        </div>
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
                    $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
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
</form>
