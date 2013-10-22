<div id="col1">
    <div id="col1_content" class="silvercart-download-page-holder clearfix">
        <% include SilvercartBreadCrumbs %>
        <h1>{$Title}</h1>
        {$Content}
        $InsertCustomHtmlForm(SilvercartDownloadSearchForm)
        Ihre Suche nach <strong>&quot;{$SearchQuery}&quot;</strong> ergab <strong>{$SearchResults.Count} Treffer</strong>.<br/>
        <br/>
        <br/>
<% if SearchResults %>
        <div class="silvercart-download-category-files visible">
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
                <% control SearchResults %>
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
