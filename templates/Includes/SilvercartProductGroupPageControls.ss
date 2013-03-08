<% if HasMoreProductsThan(0) %>
    <div class="silvercart-product-group-page-controls">
        <div class="silvercart-product-group-page-controls_content">
            <div class="subcolumns">
                <div class="c75l">
                    <div class="subcl">
                        <% include SilvercartProductPagination %>
                    </div>
                </div>

                <div class="c25r">
                    <div class="subcr">
                        <% if ActiveSilvercartProducts %>
                            <div class="silvercart-product-group-holder-toolbar clearfix">
                                <% if hasMoreGroupViewsThan(1) %>
                                    <ul>
                                        <% loop GroupViews %>
                                            <% if isActive %>
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
                                                            <a href="{$CurrentPage.Link}switchGroupView/$Code" title="$Label">
                                                                <img src="$Image" width="20" height="20" alt="$Label" />
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            <% end_if %>
                                        <% end_loop %>
                                    </ul>
                                <% end_if %>
                            </div>
                        <% else %>
                            <% if Children %>
                                <div class="silvercart-product-group-holder-toolbar clearfix">
                                    <% if hasMoreGroupHolderViewsThan(1) %>
                                        <ul>
                                            <% loop GroupHolderViews %>
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
                                                                <a href="{$CurrentPage.Link}switchGroupHolderView/$Code" title="$Label">
                                                                    <img src="$Image" width="20" height="20" alt="$Label" />
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <% end_if %>
                                            <% end_loop %>
                                        </ul>
                                    <% end_if %>
                                </div>
                            <% end_if %>
                        <% end_if %>
                    </div>
                </div>
            </div>

            <div class="silvercart-product-group-page-selectors">
                <div class="silvercart-product-group-page-selectors_content">
                    $InsertCustomHtmlForm(SilvercartProductGroupPageSelectors)
                </div>
            </div>
        </div>
    </div>
<% end_if %>
