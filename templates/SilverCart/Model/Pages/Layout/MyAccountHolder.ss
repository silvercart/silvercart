<div class="row">
    <div class="span9">
        <% include SilverCart/Model/Pages/BreadCrumbs %>
    <% if $CurrentRegisteredCustomer %>
        <div class="section-header clearfix">
            <h1>{$Title}</h1>
        </div>       
        {$Content}
        <% if Children %>
        <div id="cart-acc" class="cart-accordain">
            <% loop $Children %>
                <% if $hasSummary %>
            <div class="accordion-group">        
                <div class="accordion-heading">
                    <a href="#{$ID}" data-parent="#cart-acc" data-toggle="collapse" class="accordion-toggle" Title="{$SummaryTitle}">
                        <i class="icon-caret-right"></i> {$SummaryTitle}
                    </a>
                </div>

                <div class="accordion-body collapse in" id="{$ID}">
                    <div class="accordion-inner clearfix">                                                 
                        {$Summary}
                        <div class="control-group">
                            <div class="controls">
                                <span class="pull-right">
                                    <a class="btn btn-primary" href="{$Link}" title="<%t SilverCart\Model\Pages\MyAccountHolder.MORE 'More' %>"><%t SilverCart\Model\Pages\MyAccountHolder.MORE 'More' %></a>
                                </span>	
                            </div>
                        </div>
                    </div>            
                </div>
            </div>
                <% end_if %>
            <% end_loop %>
        </div>
        <% end_if %>
    <% else %>
        <% include SilverCart/Model/Pages/MyAccountLoginOrRegister %>
    <% end_if %>
    </div>
    <aside class="span3">
    <% if $CurrentRegisteredCustomer %>
        {$SubNavigation}
    <% end_if %>
        {$InsertWidgetArea(Sidebar)}
    </aside>
</div>




