<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <div class="row">
        <div class="col6">{$Fields.dataFieldByName(emailaddress).Field}</div>
        <div class="col6">{$Fields.dataFieldByName(password).Field}</div>
    </div>
    <a href="{$BaseHref}Security/lostpassword"><%t SilverStripe\Security\Member.BUTTONLOSTPASSWORD 'I\'ve lost my password' %></a>
    <div id="silvercart-quicklogin-form-actions">
        <input type="reset" id="silvercart-quicklogin-form-cancel" value="<%t SilverCart\Model\Pages\Page.CANCEL 'Cancel' %>" />
        <% loop $Actions %>
            {$Field}
        <% end_loop %>
    </div>
    {$CustomFormSpecialFields}
<% if $IncludeFormTag %>
</form>
<% end_if %>