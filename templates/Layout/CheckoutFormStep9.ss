<form class="yform" $FormAttributes>

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <fieldset>
        <legend><%_t('Page.ORDER_COMPLETED','Your order is completed') %></legend>

        <p>
            <% _t('Page.ORDER_THANKS','Many thanks for Your oder.') %>
        </p>
        <p>
            <% _t('Page.VIEW_ORDERS_TEXT','You can check the status of Your order any time in the') %> <a href="/<% _t('MyAccountHolder.URL_SEGMENT') %>/<% _t('OrderHolder.URL_SEGMENT') %>"><% _t('OrderHolder.SINGULARNAME') %></a>
        </p>

        $PaymentConfirmationText
	</fieldset>
</form>