<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="typography">
            <div id="Content">
                <% include SilvercartBreadCrumbs %>
                <h2>$Title</h2>
		$Content
		$Form
		$PageComments
                <% if CurrentMember %>
                <% if CustomHtmlFormStepLinkCancel %>
                <p>
                    <a href="$CustomHtmlFormStepLinkCancel"><% _t('SilvercartPage.CANCEL','cancel') %></a>
                </p>
                <% end_if %>
                <% if CustomHtmlFormStepLinkPrev %>
                <p>
                    <a href="$CustomHtmlFormStepLinkPrev"><% _t('SilvercartPage.PREV') %></a>
                </p>
                <% end_if %>
                <% if CustomHtmlFormStepLinkNext %>
                <p>
                    <a href="$CustomHtmlFormStepLinkNext"><% _t('SilvercartPage.NEXT') %></a>
                </p>
                <% end_if %>

                <% else %>
                $CheckoutLoginForm
                <div id="continueAnonymously">
                    <a href="{$BaseHref}/checkoutloginoptions/checkout-schritt-1/"><% _t('SilvercartPage.SHOP_WITHOUT_REGISTRATION','shop without registration') %></a>
                </div>
                <% end_if %>
            </div>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SilvercartSecondLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
