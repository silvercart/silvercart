<div class="product-tab">
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#productdescription"><%t SilverCart\Model\Product\Product.DESCRIPTION 'product description' %></a>
        </li>
        <% if $Files %>
        <li>
            <a data-toggle="tab" href="#downloads"><%t SilverCart\Model\Product\Product.DOWNLOADS 'Downloads' %></a>
        </li>
        <% end_if %>
        <% if $PluggedInTabs %>
            <% loop $PluggedInTabs %>
        <li>
            <a data-toggle="tab" href="#<% if $TabID %>{$TabID}<% else %>pluggedInTab{$Pos}<% end_if %>">{$Name}</a>
        </li>
            <% end_loop %>
        <% end_if %>
    </ul>
    <div class="tab-content">
        <div id="productdescription" class="tab-pane active">
            <h2 class="mobile-show-sm tab-heading"><i class="icon-caret-right"></i> <%t SilverCart\Model\Product\Product.DESCRIPTION 'Description' %></h2>
            <div class="tab-body">
                {$HtmlEncodedLongDescription}
            </div>
        </div>
        <% if $Files %>
        <div id="downloads" class="tab-pane">
            <h2 class="mobile-show-sm tab-heading"><i class="icon-caret-right"></i> <%t SilverCart\Model\Product\Product.DOWNLOADS 'Downloads' %></h2>
            <div class="tab-body">
                <table class="table full silvercart-default-table">
                    <colgroup>
                        <col width="20%"></col>
                        <col width="65%"></col>
                        <col width="15%"></col>
                    </colgroup>
                    <tr>
                        <th><%t SilverCart\Model\Product\File.TYPE 'Type' %></th>
                        <th><%t SilverCart\Model\Product\File.TITLE 'Display name' %></th>
                        <th class="align_right"><%t SilverCart\Model\Product\File.SIZE 'Size' %></th>
                    </tr>
                    <% loop $Files %>
                    <tr class="{$EvenOdd}">
                        <td>
                            <div class="silvercart-file-icon">
                                <a href="{$File.Link}">{$FileIcon}</a>
                            </div>
                        </td>
                        <td>
                            <a href="{$File.Link}">{$Title}</a>
                        </td>
                        <td class="text-right">
                            <a href="{$File.Link}">{$File.Size}</a>
                        </td>
                    </tr>
                    <% end_loop %>
                </table>
            </div>
        </div>
        <% end_if %>
        <% if $PluggedInTabs %>
            <% loop $PluggedInTabs %>
        <div id="<% if $TabID %>{$TabID}<% else %>pluggedInTab{$Pos}<% end_if %>" class="tab-pane">
            <h2 class="mobile-show-sm tab-heading"><i class="icon-caret-right"></i> {$Name}</h2>
            <div class="tab-body">
                {$Content}
            </div>
        </div>
            <% end_loop %>
        <% end_if %>
    </div>
</div>