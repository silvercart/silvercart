<div class="cms-menu cms-panel cms-panel-layout west" id="cms-menu" data-layout-type="border">
	<div class="cms-logo-header north">
		<div class="cms-logo silvercart">
			<a href="http://www.silvercart.org/" target="_blank" title="SilverCart (Version - {$SilvercartVersion}) | SilverStripe (Version - $CMSVersion)">
				SilverCart <% if SilvercartVersion %><abbr class="version">{$SilvercartVersion}</abbr><% end_if %>
			</a>
			<span><% if SiteConfig %>$SiteConfig.Title<% else %>$ApplicationName<% end_if %></span>
		</div>
	
		<div class="cms-login-status">
			<a href="Security/logout" class="logout-link" title="<% _t('LeftAndMain_Menu.ss.LOGOUT','Log out') %>"><% _t('LeftAndMain_Menu.ss.LOGOUT','Log out') %></a>
			<% with CurrentMember %>
				<span>
					<% _t('LeftAndMain_Menu.ss.Hello','Hi') %>
					<a href="{$AbsoluteBaseURL}admin/myprofile" class="profile-link ss-ui-dialog-link" data-popupclass="edit-profile-popup">
						<% if FirstName && Surname %>$FirstName $Surname<% else_if FirstName %>$FirstName<% else %>$Email<% end_if %>
					</a>
				</span>
			<% end_with %>
		</div>
	</div>
		
	<div class="cms-panel-content center">
        <ul class="cms-menu-list">
        <% loop SilvercartMenus %>
            <li<% if MenuSection %> class="active opened"<% end_if %>>
                <a href="<% with ModelAdmins.First %>$Link<% end_with %>">
                    <span class="icon icon-16 icon-{$Code.LowerCase}">&nbsp;</span>
					<span class="text">$name</span>
                </a>
                <ul class="collapse">
                <% loop ModelAdmins %>
                    <li class="$LinkingMode<% if First %> first<% end_if %>">
                        <a href="$Link">
                            <span class="icon icon-16 icon-{$Code.LowerCase}">&nbsp;</span>
                            <span class="text">$Title</span>
                        </a>
                    </li>
                <% end_loop %>
                </ul>
            </li>
        <% end_loop %>
        </ul>
	</div>
		
	<div class="cms-panel-toggle south">
		<a class="toggle-expand" href="#"><span>&raquo;</span></a>
		<a class="toggle-collapse" href="#"><span>&laquo;</span></a>
	</div>
</div>
