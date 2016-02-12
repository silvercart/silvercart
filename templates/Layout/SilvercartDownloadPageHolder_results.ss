<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
         <h1>{$Title}</h1>   
        </div>
        {$Content}
        $InsertCustomHtmlForm(SilvercartDownloadSearchForm)
        Ihre Suche nach <strong>&quot;{$SearchQuery}&quot;</strong> ergab <strong>{$SearchResults.Count} Treffer</strong>.<br/>
        <br/>
        <br/>
<% if SearchResults %>
        <div class="silvercart-download-category-files visible">
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
                <% loop SearchResults %>
                    <tr class="$EvenOdd">
                        <td>
                            <div class="silvercart-file-icon">
                                <a href="{$File.Link}" target="_blank">{$FileIcon}</a>
                            </div>
                        </td>
                        <td>
                            <a href="{$File.Link}" target="_blank">{$Title}</a>
                        </td>
                        <td class="align_right">
                            <a href="{$File.Link}" target="_blank">{$File.Size}</a>
                        </td>
                    </tr>
                <% end_loop %>
            </table>
        </div>
<% end_if %>
    </div><!--end span9-->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>