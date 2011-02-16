<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include Breadcrumbs %>
        $Content
        $Form
        $PageComments
        <% control getArticle %>
        <div class="article-page clearfix">
            <div class="article-page_content">
                <h1>$Title</h1>
                <div class="subcolumns">
                    <div class="c50l">
                        $image.SetRatioSize(230,190)
                    </div>
                    <div class="c50r">
                        <p>$ShortDescription</p>
                        <div class="article-page-details">
                            <p><strong class="price">$Price.Nice</strong><br/>
                                <% sprintf(_t('Page.TAX', 'incl. %s%% VAT'),$tax.Rate) %><br />
                                <% _t('Page.PLUS_SHIPPING','plus shipping') %><br/>
                            </p>
                        </div>
                        $Top.articleAddCartForm
                    </div>
                </div>
                <div class="article-page-description">
                    <h3><% _t('Article.DESCRIPTION','article description') %>:</h3>
                    <p>$LongDescription</p>
                </div>
            </div>
        </div>
        <% end_control %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
        <% include SideBarCart %>
        <% include ThirdLevelNavigation %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>