<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <div class="row-fluid">
        <div class="span4">{$Fields.dataFieldByName(emailaddress).addExtraClass(span12).FieldHolder}</div>
        <div class="span4">{$Fields.dataFieldByName(password).addExtraClass(span12).FieldHolder}</div>
        <div class="span4 last"></div>
    </div>
    {$CustomFormSpecialFields}
    <div class="row-fluid">
        <div class="span4">
        <% loop $Actions %>
            <button title="{$Title}" value="{$Title}" name="{$Name}" id="{$Id}" class="btn btn-small btn-primary" type="submit">{$Title}</button>
        <% end_loop %>
        </div>
        <div class="span4">
            <a href="{$BaseHref}Security/lostpassword" class="btn btn-small btn-link forgot-password-plain"><%t SilverStripe\Security\Member.BUTTONLOSTPASSWORD 'I\'ve lost my password' %></a>
        </div>
        <div class="span4 last"></div>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>