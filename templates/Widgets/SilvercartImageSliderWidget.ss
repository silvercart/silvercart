<% if slideImages %>

    <% if FrontTitle %>
    <div>
    <h2>$FrontTitle</h2>
    <% end_if %>
    <% if FrontContent %>
    $FrontContent
    </div>
    <% end_if %>

    <div class="flexslider">
        <ul class="slides">
            <% loop slideImages %>
            <li>
                <% if LinkedSite %>
                <a href="$LinkedSite.Link">
                    <% end_if %>
                    <% with Image %>
                    <img src="$URL" width="$Width" height="$Height" alt="$Title" />
                    <% end_with %>
                    <% if LinkedSite %>
                </a>
                <% end_if %>
            </li>
            <% end_loop %>
        </ul>
    </div><!--end flexslider-->
<% end_if %>
