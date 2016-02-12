<div class="row">
    <div class="span9">
        <div class="section-header clearfix">
          <h1>$Title</h1>  
        </div> 
            $Content
            <% with doConfirmation %>
                <p>$message</p>
            <% end_with %>
            $Form
    </div><!--end span9-->
    <aside class="span3">
        $InsertWidgetArea(Sidebar)
    </aside><!--end aside-->
</div>
