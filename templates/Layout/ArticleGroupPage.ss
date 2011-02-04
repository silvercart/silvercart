<div id="col1">
    <div id="col1_content" class="clearfix">
        <div class="subcolumns">
            <div class="c50l">
                <% include BreadCrumbs %>
            </div>
            <div class="c50r">
                <% if getArticles %>
                    <% include ArticlePagination %>
                <% end_if %>
            </div>
        </div>
        
        $Content
        $Form
        $PageComments
        <% if Children %>
        <div class="subcolumns">
            <% control Children %>
                <div <% if MultipleOf(3) %>class="c33r"<% else %>class="c33l"<% end_if %>>
                    <% if groupPicture %>
                        <a href="$Link">
                            $groupPicture.SetWidth(200)
                        </a>
                    <% end_if %>
                        <h3><a href="$Link">$Title</a></h3>
                </div>
                <% if MultipleOf(3) %>
        </div>
        <div style="clear:both; height:1em;"></div>
        <div class="subcolums">
                <% end_if %>
            <% end_control %>
        </div>
        <% else_if getArticles %>
            <% control getArticles %>
                <% include ArticlePreview %>
            <% end_control %>
                <% include ArticlePagination %>
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SideBarCart %>
        <% include ThirdLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>