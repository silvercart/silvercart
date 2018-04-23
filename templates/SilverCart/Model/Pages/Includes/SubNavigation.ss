<% if $SubElements %>
<div class="widget">
    <div class="categories">   
        <ul class="unstyled">
            <% loop $SubElements %>
            <li class="{$FirstLast}">
                <a href="{$Link}" class="highlight <% if $LinkingMode == current %>active<% end_if %>" title="{$Title.XML}">{$MenuTitle.XML}</a>
                <% if $Children %>
                <ul class="submenu">
                    <% loop $Children %>
                    <li class="{$FirstLast}">
                        <a href="{$Link}" class="highlight <% if $LinkingMode == current %>active<% end_if %>" title="{$Title.XML}">{$MenuTitle.XML}</a>
                    </li>
                    <% end_loop %>
                </ul>
                <% end_if %>
            </li>
            <% end_loop %>
        </ul>
    </div>
</div>
<% end_if %>
