<% if $Files %>
<div id="downloads">
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="left"><%t SilverCart\Model\Product\File.TYPE 'Type' %></th>
                <th class="left"><%t SilverCart\Model\Product\File.DESCRIPTION 'Description' %></th>
                <th class="right"><%t SilverCart\Model\Product\File.SIZE 'Size' %></th>
                <th class="right">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <% loop $Files %>
            <% include SilverCart/Model/Pages/DownloadPage_TableLine %>
        <% end_loop %>
        </tbody>
    </table>
</div>
<% end_if %>