<form class="form-horizontal grouped" $FormAttributes >
    $CustomHtmlFormMetadata

    <h4><% _t('SilvercartPage.CONTACT_FORM') %></h4>
    <div class="clearfix">
        $CustomHtmlFormFieldByName(Salutation,CustomHtmlFormFieldSelect)
        $CustomHtmlFormFieldByName(FirstName)
        $CustomHtmlFormFieldByName(Surname)
        $CustomHtmlFormFieldByName(Email)
    <% if EnablePhoneNumber %>
        $CustomHtmlFormFieldByName(Phone)
    <% end_if %>
        $CustomHtmlFormFieldByName(Message)
        $CustomHtmlFormSpecialFields
    <% loop Actions %>
        <button class="btn btn-primary pull-right" type="submit" id="{$ID}" title="$Title" value="$Title">$Title</button> 
    <% end_loop %> 
    </div>
</form>
