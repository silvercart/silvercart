<% if $PaginatedList.MoreThanOnePage %>
    <% if $FirstItem == $LastItem %>
        <%t SilverCart\Extensions\ORM\PaginatedListExtension.InfoFirstIsLast 'Page {currentPage}, item {firstItem}' currentPage=$CurrentPage firstItem=$FirstItem %>
    <% else %>
        <%t SilverCart\Extensions\ORM\PaginatedListExtension.Info 'Page {currentPage}, item {firstItem} - {lastItem}' currentPage=$CurrentPage firstItem=$FirstItem lastItem=$LastItem %>
    <% end_if %>
    <% if $TotalPages == 1 %>
    <small class="badge badge-light text-uppercase"><%t SilverCart\Extensions\ORM\PaginatedListExtension.Info2Singular '{totalItems} items on {totalPages} page' totalItems=$TotalItems totalPages=$TotalPages %></small>
    <% else %>
    <small class="badge badge-light text-uppercase"><%t SilverCart\Extensions\ORM\PaginatedListExtension.Info2Plural '{totalItems} items on {totalPages} pages' totalItems=$TotalItems totalPages=$TotalPages %></small>
    <% end_if %>
<% end_if %>
