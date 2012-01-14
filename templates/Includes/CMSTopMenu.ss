<style type="text/css">
    #silvercart-cms-mainmenu {
    }
    #silvercart-cms-mainmenu ul {
        
    }
    #silvercart-cms-mainmenu ul li {
        float: left;
        margin: 0 4px;
        height: 33px;
        cursor: pointer;
    }
    #silvercart-cms-mainmenu ul li a {
        display: block;
        height: 33px;
        float: left;
        padding: 0 6px;
        font-size: 14px;
        letter-spacing: -0.1px;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-weight: normal;
        line-height: 32px;
        color: #fff;
        text-decoration: none;
    }
    #silvercart-cms-mainmenu ul li a:hover {
		text-decoration: none;
		background: #6a7580 url(../images/mainmenu/hover.gif) repeat-x left top;
    }
    #silvercart-cms-mainmenu ul li ul {
        z-index: 99;
        display: none;
        position: absolute;
        background: #474855;
        width: 300px;
        top:   33px;
        left:  auto;
    }
    #silvercart-cms-mainmenu ul li:hover ul {
        display: block;
    }
    #silvercart-cms-mainmenu ul li ul li {
        display: block;
        margin: 0px;
        width: 100%;
    }
    #silvercart-cms-mainmenu ul li ul li a {
        float: none;
        display: block;
    }
</style>

<div id="silvercart-cms-mainmenu">
    <ul>
        <li>
            <a href="#">CMS{$CmsSection}</a>
            <ul>
                <% control SilvercartMainMenu %>
                    <li class="$LinkingMode" id="Menu-$Code"><a href="$Link">$Title</a></li>
                <% end_control %>
            </ul>
        </li>
        <li>
            <a href="#">Bestellungen</a>
            <ul>
                <li><a href="#">Bestellungen Liste</a></li>
                <li><a href="#">DHL Schnittstelle</a></li>
            </ul>
        </li>
        <li>
            <a href="#">Artikel</a>
            <ul>
                <li><a href="#">Katalog</a></li>
                <li><a href="#">Hersteller</a></li>
                <li><a href="#">Warengruppen</a></li>
            </ul>
        </li>
        <li>
            <a href="#">Shop-Einstellungen</a>
            <ul>
                <li><a href="#">Zahlung</a></li>
                <li><a href="#">Versand</a></li>
                <li><a href="#">Basiseinstellungen</a></li>
                <li><a href="#">Marketing</a></li>
                <li><a href="#">Email Vorlagen</a></li>
                <li><a href="#">Nummernkreise</a></li>
            </ul>
        </li>
    </ul>
</div>
<div id="Logo" style="$LogoStyle">
	<% if ApplicationLogoText %>
	<a href="$ApplicationLink">$ApplicationLogoText</a><br />
	<% end_if %>
</div>
