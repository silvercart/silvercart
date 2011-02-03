<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include Breadcrumbs %>
        $Content
        
        $Form
        $PageComments
        <% control getArticle %>
        <div class="subcolumns">
            <div class="c50l">
                $image.SetWidth(300)
                &nbsp;
                <p>$ShortDescription</p>
            </div>
            <div class="c50r">
                <h1>$Title</h1>
                <strong><% _t('Article.DESCRIPTION','article description') %>:</strong>
                <p>$LongDescription</p>
                <div class="acticlePreviewPrice">
                    <strong class="price">$Price.Nice</strong>
                    <p>
                        <% sprintf(_t('Page.TAX'),$tax.Rate) %><br />
                        <% _t('Page.PLUS_SHIPPING') %>
                    </p>
        <% end_control %>
                   <div class="addcartform">
                        $articleAddCartForm
                   </div>
                </div>
            </div>
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









