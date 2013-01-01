<% if ShowWidget %>
    <% if FrontTitle %>
    <div class="silvercart-widget-productgroupitems-headline">
        <h2>$FrontTitle</h2>
        </div>
    <% end_if %>

    <% if FrontContent %>
        $FrontContent.RAW
    <% end_if %>

    <div class="silvercart-product-group-page-control-top">
        <% include SilvercartProductGroupPageControls %>
    </div>
    <div class="silvercart-product-group-page">
        $RenderProductGroupPageGroupView
    </div>
    <div class="silvercart-product-group-page-control-top">
        <% include SilvercartProductGroupPageControls %>
    </div>
<% end_if %>