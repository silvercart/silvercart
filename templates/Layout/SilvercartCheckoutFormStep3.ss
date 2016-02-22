{$Controller.ContentStep3}
<form class="form-horizontal grouped" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <h4>
        <% _t('SilvercartShippingMethod.SINGULARNAME') %>
    </h4>
    $CustomHtmlFormFieldByName(ShippingMethod,CustomHtmlFormFieldOptionSet)
    <hr>
    <div class="clearfix">
    <% loop Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
    <% end_loop %>
    </div>
</form>
