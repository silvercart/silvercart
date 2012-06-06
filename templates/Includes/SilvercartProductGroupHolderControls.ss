<div class="silvercart-product-group-page-controls">
    <div class="silvercart-product-group-page-controls_content">
        <div class="subcolumns">
            <div class="c75l">
                <div class="subcl">
                    <% if ViewableChildren.MoreThanOnePage %>
                        <% include SilvercartProductGroupPagination %>
                    <% else %>
                        &nbsp;
                    <% end_if %>
                </div>
            </div>

            <div class="c25r">
                <div class="subcr">
                    <% if Children %>
                        <div class="silvercart-product-group-holder-toolbar clearfix">
                            <% if hasMoreGroupHolderViewsThan(1) %>
                                <ul>
                                    <% control GroupHolderViews %>
                                        <% if isActiveHolder %>
                                            <li class="active">
                                                <div class="silvercart-group-view-marker">
                                                    <div class="silvercart-group-view-marker_content">
                                                        <strong>
                                                            <img src="$Image" width="20" height="20" alt="$Label" />
                                                        </strong>
                                                    </div>
                                                </div>
                                            </li>
                                        <% else %>
                                            <li>
                                                <div class="silvercart-group-view-link">
                                                    <div class="silvercart-group-view-link_content">
                                                        <a href="{$Top.Link}switchGroupHolderView/$Code" title="$Label">
                                                            <img src="$Image" width="20" height="20" alt="$Label" />
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        <% end_if %>
                                    <% end_control %>
                                </ul>
                            <% end_if %>
                        </div>
                    <% end_if %>
                </div>
            </div>
        </div>
    </div>
</div>