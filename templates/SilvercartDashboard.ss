<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-language" content="$i18nLocale" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <% base_tag %>
    <title>$ApplicationName | $SectionTitle</title>
</head>

<body class="stillLoading $CSSClasses">
    <div id="Loading" style="background: #FFF url($LoadingImage) 50% 50% no-repeat; position: absolute;z-index: 100000;height: 100%;width: 100%;margin: 0;padding: 0;z-index: 100000;position: absolute;"><% _t('LOADING','Loading...',PR_HIGH) %><noscript><h1><% _t('REQUIREJS','The CMS requires that you have JavaScript enabled.',PR_HIGH) %></h1></noscript></div>

    <div id="top">
        $CMSTopMenu
    </div>

    <div id="silvercart-onePane">
        <div class="silvercart-onePane-metrics">
            <% loop DashboardFields %>
                <div class="silvercart-widget">
                    <div class="silvercart-widget_content">
                        $FieldHolder
                    </div>
                </div>
            <% end_loop %>
        </div>
        <div class="silvercart-onePane-panels">
            <h2><% _t('SilvercartDashboard.NEWS_HEADLINE') %></h2>
            <% if LatestSilvercartNews %>
                <% loop LatestSilvercartNews %>
                    <div class="silvercart-dashboard-news-entry">
                        <div class="silvercart-dashboard-news-entry_content">
                            <h3>$NewsTitle</h3>
                            <p class="date">$Date</p>
                            <p class="author">$Author</p>
                            <div class="content">
                                $Content
                            </div>
                            <div class="silvercart-button">
                                <a href="$Link"><% _t('SilvercartDashboard.NEWS_READ_MORE') %></a>
                            </div>
                        </div>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>

        <div class="silvercart-clearfix"></div>
    </div>

    <div id="bottom">
        <div class="holder">
            <div id="logInStatus">
                <a href="$ApplicationLink" title="<% _t('SSWEB','Silverstripe Website') %>">$ApplicationName</a>&nbsp;-&nbsp;
                <abbr style="border-style: none" title="<% _t('APPVERSIONTEXT1',"This is the") %> $ApplicationName <% _t('APPVERSIONTEXT2',"version that you are currently running, technically it's the CVS branch") %>">$CMSVersion</abbr> &nbsp; &nbsp; &nbsp; 
                <% with CurrentMember %>
                    <% _t('LOGGEDINAS','Logged in as') %> <strong><% if FirstName && Surname %>$FirstName $Surname<% else_if FirstName %>$FirstName<% else %>$Email<% end_if %></strong> | <a href="{$BaseHref}admin/myprofile" id="EditMemberProfile"><% _t('EDITPROFILE','Profile') %></a> | <a href="Security/logout" id="LogoutLink"><% _t('LOGOUT','Log out') %></a>
                <% end_with %>
            </div>

            <div id="switchView" class="bottomTabs">
                <% if ShowSwitchView %>
                    <div class="blank"> <% _t('VIEWPAGEIN','Page view:') %> </div>
                    <span id="SwitchView">$SwitchView</span>
                <% end_if %>
            </div>
        </div>
    </div>
</body>
</html>
