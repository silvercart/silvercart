<% if Menu(3) %>
<div class="widget">
    <div class="widget_content">
        <div class="vlist">
            <ul>
                <% control Menu(3) %>
                <% if hasProductsOrChildren %>
                <% if Children %>
                <li class="$LinkingMode">
                    <a class="active" href="$Link"  title="$Title.XML">$MenuTitle.XML</a>
                    <ul>
                        <% control Children %>
                        <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
                        <% end_control %>
                    </ul>
                </li>
                <% else %>
                <li class="$LinkingMode"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a></li>
                <% end_if %>
                <% end_if %>
                <% end_control %>
            </ul>
        </div>
    </div>
</div>
<% end_if %>
