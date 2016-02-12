<% if Menu(3) %>
<div class="widget">
    <div class="categories">
        <% if $Title %>
        <div class="section-header clearfix">
            <h3>{$Title}</h3>
        </div>
        <% end_if %>
        <ul class="unstyled">
            <% loop Menu(3) %>
            <% if hasProductsOrChildren %>
            <% if Children %>
            <li class="$LinkingMode">
                <a href="$Link" class="highlight <% if LinkingMode = current %>active <% end_if %>" title="$Title.XML">$MenuTitle.XML</a>
                <ul class="submenu">
                    <% loop Children %>
                    <li class="$LinkingMode">
                        <a href="$Link" class="highlight <% if LinkingMode = current %>active <% end_if %>" title="$Title.XML">$MenuTitle.XML</a>
                    </li>
                    <% end_loop %>
                </ul>
            </li>
            <% else %>
            <li class="$LinkingMode">
                <a href="$Link" class="highlight <% if LinkingMode = current %>active <% end_if %>" title="$Title.XML">$MenuTitle.XML</a></li>
            <% end_if %>
            <% end_if %>
            <% end_loop %>
        </ul>
    </div>
</div>
<% end_if %>
