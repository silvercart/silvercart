<% if isFilledCart %>
    <div class="widget">
        <div class="widget_content">
            <h2><% _t('Page.CART') %></h2>
                <table>
                    <tr>
                        <th><% _t('Article.TITLE') %></th>
                        <th class="Amount"><% _t('ArticlePage.QUANTITY') %></th>
                        <th class="pricewidth"><% _t('Article.PRICE') %></th>
                    </tr>
                    <% control CurrentMember %>
                        <% control shoppingCart %>
                            <% control positions %>
                            <tr>
                                <td><a href="$article.Link">$article.Title</a></td>
                                <td class="Amount">$Quantity</td>
                                <td class="pricewidth">$Price.Nice</td>
                            </tr>
                            <% end_control %>
                                <tr>
                                    <td></td>
                                    <td><% _t('Page.SUM','sum') %></td>
                                    <td class="pricewidth">$Price.Nice</td>
                                </tr>
                        <% end_control %>
                    <% end_control %>
                </table>
            <a href="$PageByClassName(CartPage).Link"><strong class="ShoppingCart"><% _t('Page.GOTO_CART', 'go to cart') %></strong></a>
        </div>
    </div>
<% end_if %>
