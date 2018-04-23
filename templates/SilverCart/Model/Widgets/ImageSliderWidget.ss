<% if $slideImages %>
    <% if $FrontTitle || $FrontContent %>
    <div>
        <% if $FrontTitle %>
            <h2>{$FrontTitle}</h2>
        <% end_if %>
        <% if FrontContent %>
            {$FrontContent}
        <% end_if %>
    </div>
    <% end_if %>

    <% if $useSlider %>
    <div class="flexslider <% if not $buildNavigation %>noControlNav<% end_if %>">
        <ul class="slides">
        <% loop $getSlideImages %>
            <li>
            <% if $LinkedSite %>
                <a href="{$LinkedSite.Link}"><% with $Image %><img src="{$URL}" width="{$Width}" height="{$Height}" alt="{$Up.AltText}" /><% end_with %></a>
            <% else %>
                <% with $Image %>
                    <img src="{$URL}" width="{$Width}" height="{$Height}" alt="{$Up.AltText}" />
                <% end_with %>
            <% end_if %>
            </li>
        <% end_loop %>
        </ul>
    </div>
    <% else %>
        <% loop $getSlideImages %>
            <div class="">
            <% if $LinkedSite %>
                <a href="{$LinkedSite.Link}"><% with $Image %><img src="{$URL}" width="{$Width}" height="{$Height}" alt="{$Up.AltText}" /><% end_with %></a>
            <% else %>
                <% with $Image %>
                    <img src="{$URL}" width="{$Width}" height="{$Height}" alt="{$Up.AltText}" />
                <% end_with %>
            <% end_if %>
            </div>
        <% end_loop %>
    <% end_if %>
<% end_if %>
