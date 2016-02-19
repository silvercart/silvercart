<div class="register">
    <form class="form-inline" $FormAttributes >
        {$CustomHtmlFormMetadata}
        $CustomHtmlFormFieldByName(backlink,CustomHtmlFormFieldHidden)
        
        <% if EnableBusinessCustomers %>
        <h4><% _t('SilvercartCustomer.BUSINESSCUSTOMER') %></h4>
            <div class="row-fluid">
                <div class="span4">
                    $CustomHtmlFormFieldByName(IsBusinessAccount,CustomHtmlFormFieldCheck)
                </div>
                <div class="span4">
                    $CustomHtmlFormFieldByName(TaxIdNumber)
                </div>
                <div class="span4 last">
                    $CustomHtmlFormFieldByName(Company)
                </div>
            </div>
        <% end_if %>
         
        <h4><% _t('SilvercartPage.ADDRESS_DATA') %></h4>
        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(FirstName)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(Surname)
            </div>
        </div>
        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Street)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(StreetNumber)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(Addition)
            </div>
        </div>    

        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Postcode)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(City)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(Country,CustomHtmlFormFieldSelect)
            </div>
        </div>    

        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(PhoneAreaCode)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(Phone)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(Fax)
            </div>
        </div>    


        <% if demandBirthdayDate %>
        <h4><% _t('SilvercartPage.BIRTHDAY') %></h4>
        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(BirthdayDay,CustomHtmlFormFieldSelect)
            </div>
            <div class="span4">
                $CustomHtmlFormFieldByName(BirthdayMonth,CustomHtmlFormFieldSelect)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(BirthdayYear)
            </div>
        </div>    
        <% end_if %>

        <h4><% _t('SilvercartPage.ACCESS_CREDENTIALS') %></h4>
        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Email)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(EmailCheck)
            </div>
        </div>    

        <div class="row-fluid">
            <div class="span4">
                $CustomHtmlFormFieldByName(Password)
            </div>
            <div class="span4 last">
                $CustomHtmlFormFieldByName(PasswordCheck)
            </div>
        </div>    





        <h4><% _t('SilvercartRegistrationPage.OTHERITEMS') %></h4>
        <div class="row-fluid">
            <div class="span12 last">
                $CustomHtmlFormFieldByName(SubscribedToNewsletter,SilvercartHasAcceptedNewsletterFieldCheck)
            </div>
          
        </div>
        
        <div class="row-fluid">
            <div class="span12 last">
                {$CustomHtmlFormSpecialFields}
            </div>
        </div>   
        <div class="margin-side clearfix">
            <% loop Actions %>
            <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
            <% end_loop %>
        </div>
    </form>
</div>