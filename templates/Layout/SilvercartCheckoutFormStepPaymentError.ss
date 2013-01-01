<form class="yform" $FormAttributes>

    <fieldset>
        <legend>
            <% if errorObject.getErrorOccured %>
                <% _t('SilvercartPage.ERROR_OCCURED','An error has occured.') %>
            <% else %>
                <% _t('SilvercartPage.PAYMENT_NOT_WORKING','The choosen payment module does not work.') %>
            <% end_if %>
        </legend>

        <% if errorObject.getErrorOccured %>
            <p>
                <br /><% _t('SilvercartPage.ERROR_LISTING','The following errors have occured:') %>
            </p>

            <div class="silvercart-error-list">
                <div class="silvercart-error-list_content">
                    <% loop errorObject.getErrorList %>
                        <p>
                            $error
                        </p>
                    <% end_loop %>
                </div>
            </div>
            <br />
        <% end_if %>

        <p>
            <% _t('SilvercartPage.CHANGE_PAYMENTMETHOD_CALL','Please choose another payment method or contact the shop owner.') %>
        </p>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    <a href="{$controller.Link}GotoStep/3" class="silvercart-button left">
                        <span class="silvercart-button_content">
                            <span class="button_type1_panel_content"><% _t('SilvercartPage.CHANGE_PAYMENTMETHOD_LINK','choose another payment method') %></span>
                        </span>
                    </a>
                </div>
            </div>
            <div class="c50r">
                <div class="subcr">
                    <a href="$PageByIdentifierCode(SilvercartContactFormPage).Link" class="silvercart-button right">
                        <span class="silvercart-button_content">
                            <span class="button_type1_panel_content"><% _t('SilvercartPage.GOTO_CONTACT_LINK','go to contact page') %></span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </fieldset>

</form>
