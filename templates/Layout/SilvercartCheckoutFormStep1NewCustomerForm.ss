<div class="form-horizontal grouped center">
    <h4><% _t('SilvercartCheckoutFormStep1.NEWCUSTOMER') %></h4>
    <form class="form left padding no-labels" $FormAttributes >

        $CustomHtmlFormMetadata

        <p><% _t('SilvercartCheckoutFormStep1.REGISTERTEXT') %></p>

        $CustomHtmlFormErrorMessages

        $CustomHtmlFormFieldByName(AnonymousOptions)

        $CustomHtmlFormSpecialFields

        <% loop Actions %>
            <button class="btn btn-small btn-primary" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
        <% end_loop %>
    </form>
</div>