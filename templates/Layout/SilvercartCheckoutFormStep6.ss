<form class="yform" $FormAttributes>

    <fieldset>
        <legend>
            <% if controller.getErrorOccured %>
                <% _t('SilvercartPage.ERROR_OCCURED') %>
            <% else %>
                <% _t('SilvercartPage.PAYMENT_NOT_WORKING') %>
            <% end_if %>
        </legend>

        <% if controller.getErrorOccured %>

            <p>
                <% _t('SilvercartPage.ERROR_LISTING') %>
            </p>

            <div class="error">
                <ul class="message">
                    <% control controller.getErrorList %>
                        <li>
                            $error
                        </li>
                    <% end_control %>
                </ul
            </div>
            <br />
        <% end_if %>

        <p>
            <% _t('SilvercartPage.CHANGE_PAYMENTMETHOD_CALL') %>
        </p>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    <a href="{$controller.Link}Cancel" class="button_type1">
                        <span class="button_type1_content">
                            <span class="button_type1_panel">
                                <span class="button_type1_panel_content"><%_t('SilvercartPage.CHANGE_PAYMENTMETHOD_LINK') %></span>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    <a href="$PageByClassName(SilvercartContactFormPage).Link" class="button_type1">
                        <span class="button_type1_content">
                            <span class="button_type1_panel">
                                <span class="button_type1_panel_content"><% _t('SilvercartPage.GOTO_CONTACT_LINK') %></span>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </fieldset>

</form>