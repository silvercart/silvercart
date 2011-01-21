<ul id="sitetree" class="tree unformatted">
    <li id="$ID" class="Root">
        <a><strong><% _t('ShopAdmin_SiteTree.ss.CONFIGURATION', 'Konfiguration') %></strong></a>
        <ul>
            <li id="record-payment" <% if Section=payment %>class="current"<% end_if %>>
                <a href="$baseURL/admin/shopadmin" title="<% _t('ShopAdmin_SiteTree.ss.PAYMENTMETHODS', 'Bezahlarten') %>"><% _t('ShopAdmin_SiteTree.ss.PAYMENTMETHODS', 'Bezahlarten') %></a>
            </li>
            <li id="record-shipping" <% if Section=shipping %>class="current"<% end_if %>>
                <a href="$baseURL/admin/shopadmin" title="<% _t('ShopAdmin_SiteTree.ss.SHIPPINGMETHODS', 'Versandarten') %>"><% _t('ShopAdmin_SiteTree.ss.SHIPPINGMETHODS', 'Versandarten') %></a>
            </li>
            <li id="record-zone" <% if Section=zone %>class="current"<% end_if %>>
                <a href="$baseURL/admin/shopadmin" title="<% _t('ShopAdmin_SiteTree.ss.ZONES', 'Zonen') %>"><% _t('ShopAdmin_SiteTree.ss.ZONES', 'Zonen') %></a>
            </li>
            <li id="record-tax" <% if Section=tax %>class="current"<% end_if %>>
                <a href="$baseURL/admin/shopadmin" title="<% _t('ShopAdmin_SiteTree.ss.TAXES', 'Steuersätze') %>"><% _t('ShopAdmin_SiteTree.ss.TAXES', 'Steuersätze') %></a>
            </li>
            <li id="record-email" <% if Section=email %>class="current"<% end_if %>>
                <a href="$baseURL/admin/shopadmin" title="<% _t('ShopAdmin_SiteTree.ss.EMAILS', 'Emails') %>"><% _t('ShopAdmin_SiteTree.ss.EMAILS', 'Emails') %></a>
            </li>
        </ul>
    </li>
</ul>
