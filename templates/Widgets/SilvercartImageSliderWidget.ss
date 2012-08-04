<% if slideImages %>
    <div>
        <% if FrontTitle %>
            <h2>$FrontTitle</h2>
        <% end_if %>
        <% if FrontContent %>
            $FrontContent
        <% end_if %>
    </div>

    <ul class="silvercart-widget-image-slider" id="SilvercartImageSliderWidget{$ID}">
        <% loop slideImages %>
            <li<% if First %><% else %> style="display: none;"<% end_if %>>
                <div>
                    <% if LinkedSite %>
                        <a href="$LinkedSite.Link">
                    <% end_if %>
                    <% with Image %>
                        <img src="$URL" width="$Width" height="$Height" alt="" />
                    <% end_with %>
                    <% if LinkedSite %>
                        </a>
                    <% end_if %>
                </div>
            </li>
        <% end_loop %>
    </ul>
<% end_if %>