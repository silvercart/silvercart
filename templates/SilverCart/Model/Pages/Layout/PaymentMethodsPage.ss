<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>
        {$Content}
        <% if $Form %>
        <div class="form">
            {$Form}
        </div>
        <% end_if %>
<% if $PaymentMethods %>
    <% loop $PaymentMethods %>
        <% if $isActive %>
        <div class="clearfix">
            <h2>{$Name}</h2>
            <% if $showPaymentLogos && $PaymentLogos.exists %>
                <div class="pull-right">
                    <% loop $PaymentLogos %>
                        {$Image.Pad(250,70)}
                    <% end_loop %>
                </div>
            <% end_if %>
            <% if $LongPaymentDescription %>
                <p class="pull-left">{$LongPaymentDescription}</p>
            <% end_if %>
        </div>
        <% end_if %>
        <hr>
    <% end_loop %>
<% end_if %>
        <div class="silvercartWidgetHolder">
            {$InsertWidgetArea(Content)}
        </div>
    </div>
    <aside class="span3">
        {$SubNavigation}
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>
