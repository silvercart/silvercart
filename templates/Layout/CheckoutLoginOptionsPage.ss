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
                    <a href="$CustomHtmlFormStepLinkCancel">Abbrechen</a>
                </p>
                <% end_if %>
                <% if CustomHtmlFormStepLinkPrev %>
                <p>
                    <a href="$CustomHtmlFormStepLinkPrev">Zurück</a>
                </p>
                <% end_if %>
                <% if CustomHtmlFormStepLinkNext %>
                <p>
                    <a href="$CustomHtmlFormStepLinkNext">Vor</a>
                </p>
                <% end_if %>

                <% else %>
                $CheckoutLoginForm
                <div id="continueAnonymously">
                    <a href="{$BaseHref}/checkoutloginoptions/checkout-schritt-1/">ohne Registrierung einkaufen</a>
                </div>
                <% end_if %>
            </div>
        </div>
    </div>
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix">

    </div>
</div>
<% end_if %>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SecondLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
