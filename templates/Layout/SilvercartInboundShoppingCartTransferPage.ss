<div id="col4">
    <div id="col4_content" class="clearfix">
        <h1>$Title</h1>
        $Content
        
        <% if ErrorMessages %>
            <div class="silvercart-error-list">
                <div class="silvercart-error-list_content">
                    <% loop ErrorMessages %>
                        <p>$Error</p>
                    <% end_loop %>
                </div>
            </div>
        <% end_if %>
    </div>
</div>
