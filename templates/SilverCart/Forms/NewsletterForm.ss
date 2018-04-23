<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <h4><%t SilverCart\Model\Pages\Page.NEWSLETTER_FORM 'Newsletter settings' %></h4>
    <div class="clearfix">
        {$Fields.dataFieldByName(Salutation).FieldHolder}
        {$Fields.dataFieldByName(FirstName).FieldHolder}
        {$Fields.dataFieldByName(Surname).FieldHolder}
        {$Fields.dataFieldByName(Email).FieldHolder}
        {$Fields.dataFieldByName(NewsletterAction).FieldHolder}
        {$CustomFormSpecialFields}
    <% loop $Actions %>
        <button class="btn btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}">{$Title}</button> 
    <% end_loop %> 
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>