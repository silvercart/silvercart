<div class="row">
    <div class="span9">
        <% include SilvercartBreadCrumbs %>
        <div class="section-header clearfix">
        <h1>$Title</h1>
        </div>
        $Content
        $Form
        $PageComments
<% if PaymentMethods %>
    <% loop PaymentMethods %>
        <% if isActive %>
        <div class="clearfix">
            <h2>$Name</h2>
            <% if showPaymentLogos %>
                <% if PaymentLogos %>
                    <div class="pull-right">
                        <% loop PaymentLogos %>
                            $Image.SetRatioSizeIfBigger(250,70)
                        <% end_loop %>
                    </div>
                <% end_if %>
            <% end_if %>
            <% if LongPaymentDescription %>
                <p class="pull-left">$LongPaymentDescription</p>
            <% end_if %>
        </div>
        <% end_if %>
        <hr>
    <% end_loop %>
<% end_if %>
    </div>
 <aside class="span3">
        $SubNavigation
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
