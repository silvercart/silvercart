<form class="yform" $FormAttributes>

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <fieldset>
        <legend><%_t('Page.ORDER_COMPLETED','Your order is completed') %></legend>

        <p>
            <% _t('Page.ORDER_THANKS','Many thanks for Your oder.') %>
        </p>
        <p>
            <% sprintf(_t('Page.VIEW_ORDERS_TEXT_AND_LINK','You can check the status of Your order any time in Your <a href="/%s/%s">order overview</a>'),_t('MyAccountHolder.URL_SEGMENT'),_t('OrderHolder.URL_SEGMENT')) %>
        </p>

        $PaymentConfirmationText
	</fieldset>
</form>