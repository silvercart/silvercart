<form class="yform" $FormAttributes>

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <fieldset>
        <legend><% _t('SilvercartPage.ORDER_COMPLETED','Your order is completed') %></legend>

        <p>
            <% _t('SilvercartPage.ORDER_THANKS','Many thanks for Your oder.') %>
        </p>
        <p>
            <% _t('SilvercartPage.VIEW_ORDERS_TEXT','You can check the status of Your order any time in the') %> <a href="$PageByIdentifierCode(SilvercartOrderHolder).Link"><% _t('SilvercartOrderHolder.SINGULARNAME') %></a>
        </p>

        $PaymentConfirmationText
	</fieldset>
</form>