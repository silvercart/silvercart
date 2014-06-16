<% if SubElements %>
<div class="widget">
    <div class="widget_content">
        <div class="silvercart-widget">
            <div class="silvercart-widget_content">
<% if SubElementsTitle %><h2>{$SubElementsTitle}</h2><% end_if %>
                <div class="vlist">
                    <ul>
                    <% loop SubElements %>
                        <li class="$LinkingMode $FirstLast"><a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                        <% if Children %>
                            <ul>
                            <% loop Children %>
                                <li class="$LinkingMode $FirstLast">
                                    <a href="$Link" title="$Title.XML">$MenuTitle.XML</a>
                                </li>
                            <% end_loop %>
                            </ul>
                        <% end_if %>
                        </li>
                    <% end_loop %>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<% end_if %>
