<footer>
    <div class="footerOuter">
        <div class="container">
            <div class="row-fluid">
            <% if FooterColumns %>
                <% loop FooterColumns %>
                    <% if Children %>
                    <div class="span3">
<!--                        <div class="section-header clearfix">
                            <h3><a href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>">{$MenuTitle.XML}</a></h3>
                        </div>-->
                        <div class="footer-links">
                            <ul class="unstyled">
                            <% loop Children %>
                                <li><a class="highlight" href="{$Link}" title="<% sprintf(_t('SilvercartPage.GOTO', 'go to %s page'),$Title.XML) %>"> <i class="icon-caret-right"></i> {$MenuTitle.XML}</a></li>          
                            <% end_loop %>
                            </ul>
                        </div>
                    </div>
                    <% end_if %>
                <% end_loop %>
            <% end_if %>
            </div><!--end row-fluid-->
        </div><!--end container--> 
    </div><!--end footerOuter-->   

    <div class="container footer">
        <div class="row">
            <div class="span12">
            <% if PaymentMethods %>
                <ul class="payment-methods inline pull-right">
                <% loop PaymentMethods %>
                    <% if showPaymentLogos %>
                        <% if PaymentLogos %>
                            <% loop PaymentLogos %>
                                <% if Image.Size %>
                    <li><a href="{$CurrentPage.PageByIdentifierCodeLink(SilvercartPaymentMethodsPage)}">$Image.SetHeight(35)</a></li>
                                <% end_if %>
                            <% end_loop %>
                        <% end_if %>
                    <% end_if %>
                <% end_loop %>
                </ul>
            <% end_if %>
                <p><a href="http://www.silvercart.org" target="_blank">SilverCart. eCommerce software. Open-source. You'll love it.</a></p>
                <% if DesktopLink %>
                <div class="margin-side margin-bottom mobile-show-sm">
                    <a class="btn btn-inverse btn-block-sm" href="{$DesktopLink(1)}"><i class="icon icon-fullscreen"></i> <% _t('SilvercartMobilePage.SwitchToDesktop') %></a>
                </div>
                <% end_if %>
            </div>
        </div>
    </div>
</footer>
