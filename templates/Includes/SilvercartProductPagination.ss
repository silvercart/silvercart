<% if Products %>
    <% if Products.MoreThanOnePage %>
        <div class="silvercart-pagination">
            <div class="silvercart-pagination_content clearfix">
                <% if Products.MoreThanOnePage %>
                    <% if Products.NotFirstPage %>
                    <div class="silvercart-pagination-link">
                        <div class="silvercart-pagination-link_content">
                            <a href="$Products.PrevLink" title="<% _t('SilvercartPage.PREV', 'Prev') %>">
                                <span>
                                    &lt;
                                </span>
                            </a>
                        </div>
                    </div>
                    <% end_if %>

                    <div>
                    <% loop Products.SilvercartPaginationSummary %>
                            <% if CurrentBool %> 
                                <div class="silvercart-pagination-marker">
                                    <div class="silvercart-pagination-marker_content">
                                        <strong>
                                            <span>$PageNum</span>
                                        </strong>
                                    </div>
                                </div>
                            <% else %>
                                    <% if Link %>
                                        <div class="silvercart-pagination-link">
                                            <div class="silvercart-pagination-link_content">
                                                <a href="$Link" title="<% sprintf(_t('SilvercartPage.GOTO_PAGE', 'go to page %s'),$PageNum) %>">
                                                    <span>$PageNum</span>
                                                </a>
                                            </div>
                                        </div>
                                    <% else %>
                                        <div class="silvercart-pagination-summary">
                                            <div class="silvercart-pagination-summary_content">
                                                <span>&hellip;</span>
                                            </div>
                                        </div>
                                    <% end_if %>
                            <% end_if %>
                    <% end_loop %>
                    </div>

                    <% if Products.NotLastPage %>
                        <div class="silvercart-pagination-link">
                            <div class="silvercart-pagination-link_content">
                                <a href="$Products.NextLink" title="<% _t('SilvercartPage.NEXT', 'Next') %>">
                                    <span>
                                        &gt;
                                    </span>
                                </a>
                            </div>
                        </div>
                    <% end_if %>
                <% end_if %>
            </div>
        </div>
    <% end_if %>
<% end_if %>
