<div class="form-horizontal grouped">
    <h4><% _t('SilvercartMyAccountHolder.ALREADY_HAVE_AN_ACCOUNT') %></h4>
    <div class="margin-side align-center">
        $InsertCustomHtmlForm(SilvercartLoginForm)
    </div>
    <h4><% _t('SilvercartMyAccountHolder.WANTTOREGISTER') %></h4>
    <div class="margin-side align-center">
        <p><% _t('SilvercartMyAccountHolder.REGISTER_ADVANTAGES_TEXT') %></p>
        <a class="btn btn-small btn-primary" href="$PageByIdentifierCodeLink(SilvercartRegistrationPage)"><% _t('SilvercartMyAccountHolder.GOTO_REGISTRATION') %></a>
    </div>
</div>
