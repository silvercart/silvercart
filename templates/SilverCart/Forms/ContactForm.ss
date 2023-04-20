<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <h4><%t SilverCart\Model\Pages\Page.CONTACT_FORM 'Contact form' %></h4>
    {$BeforeFormContent}
    <div class="clearfix">
        {$Fields.dataFieldByName('Salutation').FieldHolder}
        {$Fields.dataFieldByName('FirstName').FieldHolder}
        {$Fields.dataFieldByName('Surname').FieldHolder}
        {$Fields.dataFieldByName('Email').FieldHolder}
    <% if $ContactPage.FormFields %>
        <% loop $ContactPage.FormFields %>
            {$Up.Up.Fields.dataFieldByName($FormField.Name).FieldHolder}
        <% end_loop %>
    <% end_if %>
    <% if $HasSubjects %>
        {$Fields.dataFieldByName('ContactMessageSubjectID').FieldHolder}
    <% end_if %>
        {$Fields.dataFieldByName('Message').FieldHolder}
        {$CustomFormSpecialFields}
    <% if $EnableGoogleRecaptcha %>
        {$Fields.dataFieldByName('GoogleRecaptcha').FieldHolder}
    <% end_if %>
    <% if $EnableCloudflareTurnstyle %>
        {$Fields.dataFieldByName('CloudflareTurnstyle').FieldHolder}
    <% end_if %>
    <% if $EnableHoneyPot %>
        {$HoneyPotField.FieldHolder}
    <% end_if %>
    <% loop $Actions %>
        <button class="btn btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}">{$Title}</button>
    <% end_loop %>
    </div>
    {$AfterFormContent}
<% if $IncludeFormTag %>
</form>
<% end_if %>