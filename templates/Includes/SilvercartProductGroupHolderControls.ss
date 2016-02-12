<% if ViewableChildren.MoreThanOnePage %>
    <% include SilvercartProductGroupPagination %>
<% end_if %>

<% if Children %>
    <% if hasMoreGroupHolderViewsThan(1) %> 
        <div class="productFilter clearfix">
            <div class="displaytBy inline pull-right">
                <div class="btn-group">    
                    <% loop GroupHolderViews %>
                        <% if isActiveHolder %>
                            <a class="btn btn-primary active"  title="$Label">
                                <img src="$Image" width="20" height="20" alt="$Label" />
                            </a>           
                        <% else %>
                            <a class="btn" href="{$CurrentPage.Link}switchGroupHolderView/{$Code}" title="$Label">
                                <img src="$Image" width="20" height="20" alt="$Label" />
                            </a>  
                        <% end_if %>
                    <% end_loop %>
                </div> 
            </div> 
        </div>   
        <% end_if %>
<% end_if %>