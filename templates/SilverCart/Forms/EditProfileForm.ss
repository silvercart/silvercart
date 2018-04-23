<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>

    <h4><%t SilverCart\Model\Pages\Page.ADDRESS_DATA 'Address data' %></h4>
    <div class="control-group">
        <label class="control-label" for=""><%t SilverCart\Model\Pages\MyAccountHolder.YOUR_CUSTOMERNUMBER 'Your customer number' %></label>
        <div class="controls">
            <strong class="value">{$CurrentMember.CustomerNumber}</strong>
        </div>
    </div>
    {$Fields.dataFieldByName(Salutation).FieldHolder}
    {$Fields.dataFieldByName(FirstName).FieldHolder}
    {$Fields.dataFieldByName(Surname).FieldHolder}
    {$Fields.dataFieldByName(Email).FieldHolder}
    
    <% if $demandBirthdayDate %>
        <h4><%t SilverCart\Model\Pages\Page.BIRTHDAY 'Birthday' %></h4>
        {$Fields.dataFieldByName(BirthdayDay.FieldHolder}
        {$Fields.dataFieldByName(BirthdayMonth.FieldHolder}
        {$Fields.dataFieldByName(BirthdayYear).FieldHolder}
    <% end_if %>
    
    <h4><%t SilverCart\Model\Pages\Page.PASSWORD 'Password' %></h4>
    <div class="alert alert-info">
        <p><%t SilverCart\Model\Pages\Page.PASSWORD_CASE_EMPTY 'If You leave this field empty, Your password will not be changed.' %></p>
    </div>

    {$Fields.dataFieldByName(Password).FieldHolder}
    {$Fields.dataFieldByName(PasswordCheck).FieldHolder}

    <h4><%t SilverCart\Model\Pages\Page.NEWSLETTER 'Newsletter' %></h4>
    {$Fields.dataFieldByName(SubscribedToNewsletter).FieldHolder}

    {$CustomFormSpecialFields}
    <hr>
    <div class="control-group">
    <% loop $Actions %>
        <button class="btn btn-primary pull-right" id="{$ID}" title="{$Title}" value="{$Title}" name="{$Name}" type="submit">{$Title}</button>
    <% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>