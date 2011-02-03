<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="typography">
            <% if Results %>
            <ul id="SearchResults">
                <% control Results %>
                <li>
                    <% if MenuTitle %>
                    <h3><a class="searchResultHeader" href="$Link">$MenuTitle</a></h3>
                    <% else %>
                    <h3><a class="searchResultHeader" href="$Link">$Title</a></h3>
                    <% end_if %>
                    <% if Content %>
	          	$Content.FirstParagraph(html)
                    <% end_if %>
                    <a class="readMoreLink" href="$Link" title="Read more about &quot;{$Title}&quot;">Read more about &quot;{$Title}&quot;...</a>
                </li>
                <% end_control %>
            </ul>
            <% else %>
            <p><% _t('Page.NO_RESULTS','Sorry, but Your query did not return any results.') %></p>
            <% end_if %>

            <% if Results.MoreThanOnePage %>
            <div id="PageNumbers">
                <% if Results.NotLastPage %>
                <a class="next" href="$Results.NextLink" title="View the next page"><% _t('Page.NEXT') %></a>
                <% end_if %>
                <% if Results.NotFirstPage %>
                <a class="prev" href="$Results.PrevLink" title="View the previous page"><% _t('Page.PREV') %></a>
                <% end_if %>
                <span>
                    <% control Results.SummaryPagination(5) %>
                    <% if CurrentBool %>
	            $PageNum
                    <% else %>
                    <a href="$Link" title="View page number $PageNum">$PageNum</a>
                    <% end_if %>
                    <% end_control %>
                </span>

            </div>
            <% end_if %>
        </div>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include ThirdLevelNavigation %>
        <% include SideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>




