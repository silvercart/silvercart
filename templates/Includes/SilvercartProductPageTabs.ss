<div class="product-tab">
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#productdescription"><% _t('SilvercartProduct.DESCRIPTION','product description') %></a>
        </li>
        <% if SilvercartFiles %>
        <li>
            <a data-toggle="tab" href="#downloads"><% _t('SilvercartProduct.DOWNLOADS','Downloads') %></a>
        </li>
        <% end_if %>
        <% if PluggedInTabs %>
            <% loop PluggedInTabs %>
        <li>
            <a data-toggle="tab" href="#<% if TabID %>$TabID<% else %>pluggedInTab{$Pos}<% end_if %>">$Name</a>
        </li>
            <% end_loop %>
        <% end_if %>
    </ul>
    <div class="tab-content">
        <div itemprop="description" id="productdescription" class="tab-pane active">
            <h2 class="mobile-show-sm tab-heading"><i class="icon-caret-right"></i> <% _t('SilvercartProduct.DESCRIPTION') %></h2>
            <div class="tab-body">
                {$HtmlEncodedLongDescription}
            </div>
        </div>
        <% if SilvercartFiles %>
        <div id="downloads" class="tab-pane">
            <h2 class="mobile-show-sm tab-heading"><i class="icon-caret-right"></i> <% _t('SilvercartProduct.DOWNLOADS') %></h2>
            <div class="tab-body">
                <table class="table full silvercart-default-table">
                    <colgroup>
                        <col width="20%"></col>
                        <col width="65%"></col>
                        <col width="15%"></col>
                    </colgroup>
                    <tr>
                        <th><% _t('SilvercartFile.TYPE') %></th>
                        <th><% _t('SilvercartFile.TITLE') %></th>
                        <th class="align_right"><% _t('SilvercartFile.SIZE') %></th>
                    </tr>
                    <% loop SilvercartFiles %>
                    <tr class="$EvenOdd">
                        <td>
                            <div class="silvercart-file-icon">
                                <a href="$File.Link">$FileIcon</a>
                            </div>
                        </td>
                        <td>
                            <a href="$File.Link">$Title</a>
                        </td>
                        <td class="align_right">
                            <a href="$File.Link">$File.Size</a>
                        </td>
                    </tr>
                    <% end_loop %>
                </table>
            </div>
        </div>
        <% end_if %>
        <% if PluggedInTabs %>
            <% loop PluggedInTabs %>
        <div id="<% if TabID %>$TabID<% else %>pluggedInTab{$Pos}<% end_if %>" class="tab-pane">
            <h2 class="mobile-show-sm tab-heading"><i class="icon-caret-right"></i> {$Name}</h2>
            <div class="tab-body">
                {$Content}
            </div>
        </div>
            <% end_loop %>
        <% end_if %>
    </div>
</div>