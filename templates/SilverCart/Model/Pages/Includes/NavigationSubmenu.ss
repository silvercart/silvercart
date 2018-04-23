<% if $Children %>
<div>
    <ul>
    <% loop $Children %>
        <li class="{$LinkOrSection}">
        <% if $Children %>
            <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO 'Go to {title} page' title=$Title.XML %>"> <span>-</span> {$MenuTitle.XML} ({$Children.Count}) <i class="icon-caret-right pull-right"></i></a>
            <div>
               <ul>
            <% loop $Children %>
                   <li class="{$LinkOrSection}"><a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO 'Go to {title} page' title=$Title.XML %>"> <span>-</span> {$MenuTitle.XML} <% if $Children %>({$Children.Count})<% end_if %></a></li>
            <% end_loop %>
               </ul>
            </div>
        <% else %>
            <a href="{$Link}" title="<%t SilverCart\Model\Pages\Page.GOTO 'Go to {title} page' title=$Title.XML %>"><span>-</span> {$MenuTitle.XML}</a>
        <% end_if %>
        </li>
    <% end_loop %>
    </ul>
</div>
<% end_if %>