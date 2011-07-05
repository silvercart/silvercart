<% if ViewableChildren %>
    <% if ViewableChildren.MoreThanOnePage %>
        <div class="silvercart-pagination">
            <div class="silvercart-pagination_content clearfix">
                <% if ViewableChildren.PrevLink %>
                    <div class="silvercart-pagination-link">
                        <div class="silvercart-pagination-link_content">
                            <a href="$ViewableChildren.PrevLink" title="<% _t('SilvercartPage.PREV', 'Prev') %>">
                                <span>
                                    &lt;
                                </span>
                            </a>
                        </div>
                    </div>
                <% end_if %>
                <% control ViewableChildren.Pages %>
                    <% if CurrentBool %>
                        <div class="silvercart-pagination-marker">
                            <div class="silvercart-pagination-marker_content">
                                <strong>
                                    <span>
                                        $PageNum
                                    </span>
                                </strong>
                            </div>
                        </div>
                    <% else %>
                        <div class="silvercart-pagination-link">
                            <div class="silvercart-pagination-link_content">
                                <a href="$Link" title="Go to page $PageNum">
                                    <span>
                                        $PageNum
                                    </span>
                                </a>
                            </div>
                        </div>
                    <% end_if %>
                <% end_control %>
                <% if ViewableChildren.NextLink %>
                    <div class="silvercart-pagination-link">
                        <div class="silvercart-pagination-link_content">
                            <a href="$ViewableChildren.NextLink" title="<% _t('SilvercartPage.NEXT', 'Next') %>">
                                <span>
                                    &gt;
                                </span>
                            </a>
                        </div>
                    </div>
                <% end_if %>
            </div>
        </div>
    <% end_if %>
<% end_if %>
