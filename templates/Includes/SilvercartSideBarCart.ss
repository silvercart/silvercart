<% if isFilledCart %>
    <div class="widget">
        <div class="widget_content side-bar-cart">
            <h3><% _t('SilvercartPage.CART') %></h3>
            <table class="full">
                <thead>
                    <tr>
                        <th><% _t('SilvercartArticle.TITLE') %></th>
                        <th class="Amount"><% _t('SilvercartArticlePage.QUANTITY') %></th>
                        <th class="pricewidth"><% _t('SilvercartArticle.PRICE') %></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td></td>
                        <td><% _t('SilvercartPage.SUM','sum') %></td>
                        <td class="pricewidth">$Price.Nice</td>
                    </tr>
                </tfoot>
				<% control CurrentMember %>
					<% control SilvercartShoppingCart %>
						<tbody>
							<% control SilvercartPositions %>
								<tr>
									<td><a href="$SilvercartArticle.Link">$SilvercartArticle.Title</a></td>
									<td class="Amount">$Quantity</td>
									<td class="pricewidth">$Price.Nice</td>
								</tr>
							<% end_control %>
						</tbody>
					<% end_control %>
				<% end_control %>
            </table>
            <a href="{$baseHref}warenkorb"><strong class="ShoppingCart"><% _t('SilvercartPage.GOTO_CART', 'go to cart') %></strong></a>
        </div>
    </div>
<% end_if %>
