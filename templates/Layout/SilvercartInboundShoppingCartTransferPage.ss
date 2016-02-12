<div class="row">
    <div class="span4">
        <div class="section-header clearfix">
        <h1>$Title</h1>
        </div>
        $Content
        
        <% if ErrorMessages %>
            <div class="silvercart-error-list">
                <div class="silvercart-error-list_content">
                    <% loop ErrorMessages %>
                    <div class="alert alert-error"><p>$Error</p></div>
                    <% end_loop %>
                </div>
            </div>
        <% end_if %>
    </div>
</div>
