<% if slideImages %>
    <div>
        <% if FrontTitle %>
            <strong class="h2">$FrontTitle</strong>
        <% end_if %>
        <% if FrontContent %>
            $FrontContent
        <% end_if %>
    </div>
    <% if useSlider %><% else %>
    <div class="noslider">
    <% end_if %>
    <ul class="silvercart-widget-image-slider" id="SilvercartImageSliderWidget{$ID}">
        <% control slideImages %>
            <li<% if First %><% else %> style="display: none;"<% end_if %>>
                <div>
                    <% if LinkedSite %>
                        <a href="$LinkedSite.Link">
                    <% end_if %>
                        <img src="{$Image.URL}" width="{$Image.Width}" height="{$Image.Height}" alt="{$AltText}" />
                    <% if LinkedSite %>
                        </a>
                    <% end_if %>
                </div>
            </li>
        <% end_control %>
    </ul>
    <% if useSlider %><% else %>
    </div>
    <% end_if %>
<% end_if %>