<% if isFilledCart %>
    <div class="widget">
        <div class="widget_content side-bar-cart">
            <h3><% _t('Page.CART') %></h3>
            <table class="full">
                <thead>
                    <tr>
                        <th><% _t('Article.TITLE') %></th>
                        <th class="Amount"><% _t('ArticlePage.QUANTITY') %></th>
                        <th class="pricewidth"><% _t('Article.PRICE') %></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td></td>
                        <td><% _t('Page.SUM','sum') %></td>
                        <td class="pricewidth">$Price.Nice</td>
                    </tr>
                </tfoot>
<% control CurrentMember %>
    <% control shoppingCart %>
                <tbody>
        <% control positions %>
                    <tr>
                        <td><a href="$article.Link">$article.Title</a></td>
                        <td class="Amount">$Quantity</td>
                        <td class="pricewidth">$Price.Nice</td>
                    </tr>
        <% end_control %>
                </tbody>
    <% end_control %>
<% end_control %>
            </table>
            <a href="{$baseHref}warenkorb"><strong class="ShoppingCart"><% _t('Page.GOTO_CART', 'go to cart') %></strong></a>
        </div>
    </div>
<% end_if %>
