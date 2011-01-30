<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="$ContentLocale" lang="$ContentLocale">
    <head>
        <% base_tag %>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title><% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %></title>
		$MetaTags(false)
    </head>
    <body>
        <div id="headerbar">
            <div class="headerbar_margins">
                <div class="headerbar_content">
                    <div class="subcolumns">
                        <div class="c50l">
                            <div class="subcolumns">
                                <div class="c33l">
                                    <% include MetaNavigation %>
                                </div>
                                <div class="c66r">
                                    <div class="subcolumns">
                                        <div class="c33l">
                                            <div class="subcr">Finden:</div>
                                            
                                        </div>
                                        <div class="c66r">
                                            <div id="SearchForm_SearchForm">$InsertCustomHtmlForm(QuickSearch)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="c50r">
                            <div class="subcolumns">
                                <div class="c50l" id="Customer">
                                    <% if MemberInformation %>
                                    <a class="button" id="myaccount" href="/meinkonto/">Kundenbereich</a>
                                    <a class="button" id="logout" href="/home/logout/">Logout</a>
                                    <% else %>
                                    <% include LoginPopup %>
                                    <% end_if %>
                                </div>
                                <div class="c50r" id="Shopping_Checkout">
                                    <div class="subcolumns">
                                        <div class="c50l">
                                            <a class="button" id="scart" href="/warenkorb"><% if CurrentMember %> Warenkorb ($getCount) <% else %> Warenkorb (0) <% end_if %></a>
                                        </div>
                                        <div class="c50r">
                                            <a class="button" href="/checkout/">zur Kasse</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page_margins">
            <div class="page">
                <div id="header">
                    <div class="subcolumns">
                        <div class="c50l">
                            <a href="/?locale=$Locale"><img src="/silvercart/images/logo.jpg" alt="logo Pour La Table" /></a>
                        </div>
                        <div class="c50r">
                            <div class="subcr">
                                <% if Translations %>
                                <ul class="translations">
                                    <% control Translations %>
                                    <li class="$Locale.RFC1766">
                                        <a href="$Link" hreflang="$Locale.RFC1766" title="<% sprintf(_t('SHOWINPAGE','Sprache auf %s setzen'),$Locale.Nice) %>"><img alt="$Locale.Nice" src="/silvercart/images/icons/flags/{$Locale}.png" /></a>
                                    </li>
                                    <% end_control %>
                                </ul>
                                <% end_if %>
                            </div>
                        </div>
                    </div>
                    <% if Menu(1) %>
                    <div id="nav">
                        <a id="navigation" name="navigation"></a>
                        <% include Navigation %>
                    </div>
                    <% end_if %>
                </div>
                <div id="main">
                    $Layout
                </div>
                <div class="clearfix">&nbsp;</div>
            </div>
        </div>
        <p/>
        <div id="CompanyInformations">
            <% control Page(metanavigation) %>
            <% control Children %>
            <% if Last %>
            <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela">$MenuTitle.XML</a>
            <% else %>
            <a href="$Link" title="Gehe zur $Title.XML Seite" class="$LinkingMode levela">$MenuTitle.XML</a> |
            <% end_if %>
            <% end_control %>
            <% end_control %>
        </div>
        <p/>
    </body>
</html>
