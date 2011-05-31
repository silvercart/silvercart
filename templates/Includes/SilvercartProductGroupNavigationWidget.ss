<h2><% _t('SilvercartProductGroupNavigationWidget.TITLE') %></h2>

<% control Navigation %>
    <div class="vlist">
        <ul>
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
        </ul>
    </div>
<% end_control %>
