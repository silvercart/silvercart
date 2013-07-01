<% if HasMoreProductsThan(0) %>
    <div class="silvercart-product-group-page-controls">
        <div class="silvercart-product-group-page-controls_content">
            <% include SilvercartProductGroupPageControls %>

            <div class="silvercart-product-group-page-selectors">
                <div class="silvercart-product-group-page-selectors_content">
                    $InsertCustomHtmlForm(SilvercartProductGroupPageSelectors)
                </div>
            </div>
        </div>
    </div>
<% end_if %>
