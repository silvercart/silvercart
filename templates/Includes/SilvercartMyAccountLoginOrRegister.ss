<div class="form-vertical grouped">
    <h4><% _t('SilvercartMyAccountHolder.ALREADY_HAVE_AN_ACCOUNT') %></h4>
    <div class="margin-side">
        $InsertCustomHtmlForm(SilvercartLoginForm)
    </div>

    <h4><% _t('SilvercartMyAccountHolder.WANTTOREGISTER') %></h4>
    <div class="row-fluid">
        <div class="span4">
            <a class="btn btn-small btn-primary" href="$PageByIdentifierCodeLink(SilvercartRegistrationPage)"><% _t('SilvercartMyAccountHolder.GOTO_REGISTRATION') %></a>
        </div>
        <div class="span8 last">
            <% _t('SilvercartMyAccountHolder.REGISTER_ADVANTAGES_TEXT') %>
        </div>
    </div>    
</div>
<br><br>