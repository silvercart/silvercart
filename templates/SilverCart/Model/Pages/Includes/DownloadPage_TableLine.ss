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