<% control Elements %>
	<div class="article-group-holder-entry clearfix $EvenOdd">
		<div class="article-group-holder-entry_content">
			<h3><a href="$Link">$Title</a></h3>
			<div class="subcolumns clearfix">
				<div class="c33l article-group-holder-entry-image">
					<div class="subcl">
						<% if groupPicture %>
							<a href="$Link">$groupPicture.SetRatioSize(210,210)</a>
						<% end_if %>
					</div>
				</div>
				<div class="c66r">
					<div class="subcr">
						$Content
					</div>
				</div>
			</div>
			<div class="article-group-holder-entry-foot">
				<% if hasProductCount(0) %>
				<% else %>
					<% if hasProductCount(1) %>
						<a href="$Link" title="$articles.Count <% _t('SilvercartArticle.SINGULARNAME','article') %>">$SilvercartArticles.Count <% _t('SilvercartArticle.SINGULARNAME','article') %> &gt;&gt;</a>
					<% else %>
						<a href="$Link" title="$articles.Count <% _t('SilvercartArticle.PLURALNAME','article') %>">$SilvercartArticles.Count <% _t('SilvercartArticle.PLURALNAME','article') %> &gt;&gt;</a>
					<% end_if %>
				<% end_if %>
			</div>
		</div>
	</div>
<% end_control %>
