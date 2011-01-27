<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="typography">
            <h1>Suchergebnisse</h1>
            <% if SearchQuery %>
            <p>Suchergebnisse f&uuml;r den Begriff <b>&rdquo;$SearchQuery&rdquo;</b>:
            <% end_if %>

            <% if getArticles %>
                <% include ArticlePagination %>
                <% control getArticles %>
                    <% include ArticlePreview %>
                <% end_control %>
            <% else %>
                <p>Zum Suchbegriff <b>$SearchQuery</b> gibt es in unserem Shop keinen Treffer.</p>
            <% end_if %>
            <% include ArticlePagination %>
        </div>
    </div>
</div>
<% if LayoutType = 4 %>
<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>
<% end_if %>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include ThirdLevelNavigation %>
        <% include SideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>