<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="typography">
            <div id="Content">
                <% include BreadCrumbs %>
                <h2>$Title</h2>
		$Content
		$Form
		$PageComments
                <% if CurrentMember %>
                $InsertCustomHtmlForm
                <% if CustomHtmlFormStepLinkCancel %>
                <p>
                    <a href="$CustomHtmlFormStepLinkCancel"><% _t('Page.CANCEL','cancel') %></a>
                </p>
                <% end_if %>
                <% if CustomHtmlFormStepLinkPrev %>
                <p>
                    <a href="$CustomHtmlFormStepLinkPrev"><% _t('Page.PREV') %></a>
                </p>
                <% end_if %>
                <% if CustomHtmlFormStepLinkNext %>
                <p>
                    <a href="$CustomHtmlFormStepLinkNext"><% _t('Page.NEXT') %></a>
                </p>
                <% end_if %>

                <% else %>
                $CheckoutLoginForm
                <div id="continueAnonymously">
                    <a href="{$BaseHref}/checkoutloginoptions/checkout-schritt-1/"><% _t('Page.SHOP_WITHOUT_REGISTRATION','shop without registration') %></a>
                </div>
                <% end_if %>
            </div>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SecondLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
