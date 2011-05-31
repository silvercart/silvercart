<% if SubElements %>
<div class="widget">
    <div class="widget_content">
        <div class="silvercart-widget">
            <div class="silvercart-widget_content">
                <div class="vlist">
                    <ul>
                    <% control SubElements %>
                        <li class="$LinkingMode $FirstLast"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                        <% if Children %>
                            <ul>
                            <% control Children %>
                                <li class="$LinkingMode $FirstLast">
                                    <a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                                </li>
                            <% end_control %>
                            </ul>
                        <% end_if %>
                        </li>
                    <% end_control %>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<% end_if %>
