<span class="badge badge-light text-uppercase">
    <% if $FirstItem == $LastItem %>
        <%t SilverCart\Extensions\ORM\PaginatedListExtension.InfoFirstIsLast 'Page {currentPage}, item {firstItem}' currentPage=$CurrentPage firstItem=$FirstItem %>
    <% else %>
        <%t SilverCart\Extensions\ORM\PaginatedListExtension.Info 'Page {currentPage}, item {firstItem} - {lastItem}' currentPage=$CurrentPage firstItem=$FirstItem lastItem=$LastItem %>
    <% end_if %>
    <span class="font-weight-normal border-left border-dark ml-2 pl-2">
    <% if $TotalPages == 1 %>
    <%t SilverCart\Extensions\ORM\PaginatedListExtension.Info2Singular '{totalItems} items on {totalPages} page' totalItems=$TotalItems totalPages=$TotalPages %>
    <% else %>
    <%t SilverCart\Extensions\ORM\PaginatedListExtension.Info2Plural '{totalItems} items on {totalPages} pages' totalItems=$TotalItems totalPages=$TotalPages %>
    <% end_if %>
    </span>
</span>
