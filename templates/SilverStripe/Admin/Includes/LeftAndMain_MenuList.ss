<ul class="cms-menu__list">
    <% loop $SilvercartMenus %>
        <li class="$LinkingMode $FirstLast <% if $LinkingMode == 'link' %><% else %>opened<% end_if %>" id="Menu-$Code" title="$Title.ATT">
            <a href="{$ModelAdmins.first.Link}" $AttributesHTML>
                <!-- span class="icon icon-16 icon-{$Code.LowerCase}">&nbsp;</span -->
				<% if $ModelAdmins.first.IconClass %>
					<span class="menu__icon $ModelAdmins.first.IconClass"></span>
				<% else %>
					<span class="menu__icon icon icon-16 icon-{$Icon}">&nbsp;</span>
				<% end_if %>
				<span class="text">$name</span>
            </a>
            <ul class="cms-menu__list collapse">
            <% loop ModelAdmins %>
                <li class="{$LinkingMode} <% if $first %>first<% end_if %>" rel="menu-section-{$MenuCode.LowerCase}">
                    <a href="$Link">
                        <!-- span class="icon icon-16 icon-{$Code.LowerCase}">&nbsp;</span -->
                        <% if IconClass %>
                            <span class="menu__icon $IconClass"></span>
                        <% else %>
                            <span class="menu__icon icon icon-16 icon-{$Icon}">&nbsp;</span>
                        <% end_if %>
                        <span class="text">$Title</span>
                    </a>
                </li>
            <% end_loop %>
            </ul>
        </li>
    <% end_loop %>
</ul>
