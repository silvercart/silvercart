<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include SilvercartBreadCrumbs %>

            <h1>$Title</h1>
            $Content
            
            <% if SilvercartFiles %>
                <div id="downloads">
                    <table class="full silvercart-default-table">
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
                        <% control SilvercartFiles %>
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
                        <% end_control %>
                    </table>
                </div>
            <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        $InsertWidgetArea(Sidebar)
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>
