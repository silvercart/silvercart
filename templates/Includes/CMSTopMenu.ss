<style type="text/css">
    body {
        background:                     #999;
    }
    body.ModelAdmin #left,
    #left {
        width: 280px;
        border-color:                   #fff;
        border-radius:                  3px;
        -moz-border-radius:             3px;
        -webkit-border-radius:          3px;
    }
    #right {
        border-color:                   #fff;
        border-radius:                  3px;
        -moz-border-radius:             3px;
        -webkit-border-radius:          3px;
    }
    #top {
        background:                     #f1f1f1;
        background:                     -webkit-gradient(linear, left top, left bottom, from(#ffffff), to(#b9b9b9)); /* for webkit browsers */
        background:                     -moz-linear-gradient(top, #ffffff,  #b9b9b9); /* for firefox 3.6+ */
        background:                     -o-linear-gradient(top, #ffffff,  #b9b9b9); /* for opera */
        filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#b9b9b9'); /* for IE */
        border-bottom:                  1px #fff solid;
    }
    #bottom {
        background:                     #4687a4;
        background:                     -webkit-gradient(linear, left top, left bottom, from(#1c587a), to(#4687a4)); /* for webkit browsers */
        background:                     -moz-linear-gradient(top, #1c587a, #4687a4); /* for firefox 3.6+ */
        background:                     -o-linear-gradient(top, #1c587a, #4687a4); /* for opera */
        filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr='#1c587a', endColorstr='#4687a4'); /* for IE */
        border-top:                     1px #fff solid;
    }
    #left h2,
    #contentPanel h2 {
        background:                     #4687a4;
        background:                     -webkit-gradient(linear, left top, left bottom, from(#4687a4), to(#1c587a)); /* for webkit browsers */
        background:                     -moz-linear-gradient(top, #4687a4, #1c587a); /* for firefox 3.6+ */
        background:                     -o-linear-gradient(top, #4687a4, #1c587a); /* for opera */
        filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr='#4687a4', endColorstr='#1c587a'); /* for IE */
    }
    #silvercart-cms-mainmenu-logo {
        float: left;
    }
    #silvercart-cms-mainmenu-logo a {
        position: relative;
        display: block;
        line-height: 11px;
        text-decoration: none;
        color: #333;
        margin: 0px;
        padding: 0px 8px 0px 27px;
    }
    #silvercart-cms-mainmenu-logo a img {
        position: absolute;
        left: 0px;
        top: 0px;
    }
    #silvercart-cms-mainmenu-logo a span {
        display: block;
        padding: 6px 0px 0px 0px;
    }
    #silvercart-cms-mainmenu ul {
        float: left;
    }
    #silvercart-cms-mainmenu ul li {
        float: left;
        margin: 0;
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
        color: #333;
        text-decoration: none;
        border-left: 1px #fff solid;
    }
    #silvercart-cms-mainmenu ul li:hover,
    #silvercart-cms-mainmenu ul li.active:hover {
        background:                     #4687a4;
        background:                     -webkit-gradient(linear, left top, left bottom, from(#4687a4), to(#1c587a)); /* for webkit browsers */
        background:                     -moz-linear-gradient(top, #4687a4, #1c587a); /* for firefox 3.6+ */
        background:                     -o-linear-gradient(top, #4687a4, #1c587a); /* for opera */
        filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr='#4687a4', endColorstr='#1c587a'); /* for IE */
    }
    #silvercart-cms-mainmenu ul li.active {
        background:                     #555;
        background:                     -webkit-gradient(linear, left top, left bottom, from(#888), to(#555)); /* for webkit browsers */
        background:                     -moz-linear-gradient(top, #888, #555); /* for firefox 3.6+ */
        background:                     -o-linear-gradient(top, #888, #555); /* for opera */
        filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr='#888888', endColorstr='#555555'); /* for IE */
    }
    #silvercart-cms-mainmenu ul li:hover a {
        color: #fff;
    }
    #silvercart-cms-mainmenu ul li.active a {
        color: #fff;
    }
    #silvercart-cms-mainmenu ul li a:hover {
        text-decoration: none;
        background:                     #4687a4;
        background:                     -webkit-gradient(linear, left top, left bottom, from(#4687a4), to(#1c587a)); /* for webkit browsers */
        background:                     -moz-linear-gradient(top, #4687a4, #1c587a); /* for firefox 3.6+ */
        background:                     -o-linear-gradient(top, #4687a4, #1c587a); /* for opera */
        filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr='#4687a4', endColorstr='#1c587a'); /* for IE */
    }
    #silvercart-cms-mainmenu ul li ul {
        z-index: 99;
        display: none;
        clear: both;
        position: absolute;
        color: #fff;
        background: #1c587a;
        width: 300px;
        top:   33px;
        left:  auto;
        margin-left: 0px;
        border-left: 1px #fff solid;
        border-right: 1px #fff solid;
        border-bottom: 1px #fff solid;
    }
    #silvercart-cms-mainmenu ul li:hover ul {
        display: block;
    }
    #silvercart-cms-mainmenu ul li ul li {
        display: block;
        margin: 0px;
        width: 100%;
        height: auto;
    }
    #silvercart-cms-mainmenu ul li ul li.section {
        height: auto;
        border-bottom: 1px #ccc solid;
    }
    #silvercart-cms-mainmenu ul li ul li.section p {
        font-size: 9px;
        margin: 0px;
        padding: 10px 6px 4px 6px;
    }
    #silvercart-cms-mainmenu ul li ul li a {
        float: none;
        display: block;
        height: auto;
        font-size: 13px;
        letter-spacing: 0px;
        line-height: 100%;
        border-left: none;
        padding: 6px 6px;
    }
    @media print {
        #silvercart-cms-mainmenu,
        #separator,
        #left {
            display: none;
        }
        #right {
            left: 0px !important;
        }
    }
</style>

<script type="text/javascript">
    function activateOrderPositionTab() {
        alert("Call ok");
    }
</script>

<div id="silvercart-cms-mainmenu">
    <div id="silvercart-cms-mainmenu-logo">
        <a href="/admin/silvercart-dashboard">
            <img src="/silvercart/images/logo_storeadmin.png" width="25" height="33" alt="SilverCart" />
            <% if ApplicationLogoText %>
                <span>$ApplicationLogoText</span>
            <% end_if %>
        </a>
    </div>
    <ul>
        <% control SilvercartMenus %>
            <li<% if MenuSection %> class="active"<% end_if %>>
                <% control ModelAdmins.First %><a href="$Link"><% end_control %>
                    $name{$MenuSection}</a>
                <ul>
                    <% control ModelAdmins %>
                        <% if IsSection %>
                            <li class="section">
                                <p>$name</p>
                            </li>
                        <% else %>
                            <li class="$LinkingMode"><a href="$Link">$Title</a></li>
                        <% end_if %>
                    <% end_control %>
                </ul>
            </li>
        <% end_control %>
        <li<% if CmsSection %> class="active"<% end_if %>>
            <% control SilvercartMainMenu.First %>
                <a href="$Link">CMS{$Top.CmsSection}</a>
            <% end_control %>
            <ul>
                <% control SilvercartMainMenu %>
                    <li class="$LinkingMode" id="Menu-$Code"><a href="$Link">$Title</a></li>
                <% end_control %>
            </ul>
        </li>
    </ul>
</div>
