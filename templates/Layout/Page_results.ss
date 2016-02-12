<div class="row">
    <div class="span9">
            <% if Results %>
				<ul id="SearchResults">
					<% loop Results %>
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
					<% end_loop %>
				</ul>
            <% else %>
            <div class="alert alert-error">
				<p><% _t('SilvercartPage.NO_RESULTS','Sorry, but Your query did not return any results.') %></p>
            </div>
            <% end_if %>

            <% if Results.MoreThanOnePage %>
				<div id="PageNumbers">
					<% if Results.NotLastPage %>
						<a class="next" href="$Results.NextLink" title="View the next page"><% _t('SilvercartPage.NEXT') %></a>
					<% end_if %>
					<% if Results.NotFirstPage %>
						<a class="prev" href="$Results.PrevLink" title="View the previous page"><% _t('SilvercartPage.PREV') %></a>
					<% end_if %>
					<span>
						<% loop Results.SummaryPagination(5) %>
							<% if CurrentBool %>
								$PageNum
							<% else %>
								<a href="$Link" title="View page number $PageNum">$PageNum</a>
							<% end_if %>
						<% end_loop %>
					</span>
				</div>
            <% end_if %>

    </div>
    <aside class="span3">
        <% include SilvercartSideBarCart %> 
    </aside><!--end aside-->
</div>