<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
            <h1>$Title</h1>
        </div>
            
            $Content
            
            <% if SilvercartFiles %>
                <div id="downloads">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="left"><% _t('SilvercartFile.TYPE') %></th>
                                <th class="left"><% _t('SilvercartFile.DESCRIPTION') %></th>
                                <th class="right"><% _t('SilvercartFile.SIZE') %></th>
                                <th class="right">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <% loop SilvercartFiles %>
                            <tr>
                                <td class="left"><a href="{$File.Link}">{$FileIcon}</a></td>
                                <td class="left">
                                <% if $Thumbnail %>
                                    <a href="{$File.Link}"><img src="{$Thumbnail.SetWidth(100).URL}" class="pull-left thumbnail" alt="{$Title}" /></a>
                                <% end_if %>
                                    <h2><a href="{$File.Link}">{$Title}</a></h2>{$Description}
                                </td>
                                <td class="right nowrap"><a href="{$File.Link}">{$File.Size}</a></td>
                                <td class="right nowrap"><a href="{$File.Link}" class="btn btn-primary"><span class="icon-download"></span> Download</a></td>
                            </tr>
                        <% end_loop %>
                        </tbody>
                    </table>
                </div>
            <% end_if %>
     </div><!--end span9-->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
